<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class MigrateS3MediaToLocal extends Command
{
    protected $signature = 'media:migrate-s3-to-local {--dry-run : Show what would be migrated without copying files}';

    protected $description = 'Copy media-library files from S3 to the public disk and update their disk references';

    public function handle(): int
    {
        $query = Media::query()->where(function ($query) {
            $query->where('disk', 's3')
                ->orWhere('conversions_disk', 's3');
        });

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No S3 media records found.');
            return self::SUCCESS;
        }

        $this->info(($this->option('dry-run') ? 'Would migrate ' : 'Migrating ') . $total . ' media record(s).');

        $migrated = 0;
        $failed = 0;

        $query->orderBy('id')->chunkById(100, function ($mediaItems) use (&$migrated, &$failed) {
            foreach ($mediaItems as $media) {
                try {
                    $relativePath = $media->getPathRelativeToRoot();
                    $directory = dirname($relativePath);
                    $files = Storage::disk('s3')->allFiles($directory === '.' ? '' : $directory);

                    if ($media->disk === 's3' && !in_array($relativePath, $files, true)) {
                        throw new \RuntimeException("Original file not found on S3: {$relativePath}");
                    }

                    if ($files === []) {
                        throw new \RuntimeException("No media files found on S3 in: {$directory}");
                    }

                    if ($this->option('dry-run')) {
                        $this->line("[{$media->id}] {$relativePath} (" . count($files) . ' file(s))');
                        continue;
                    }

                    foreach ($files as $file) {
                        $stream = Storage::disk('s3')->readStream($file);

                        if ($stream === false) {
                            throw new \RuntimeException("Unable to read S3 file: {$file}");
                        }

                        try {
                            if (!Storage::disk('public')->writeStream($file, $stream)) {
                                throw new \RuntimeException("Unable to write local file: {$file}");
                            }
                        } finally {
                            if (is_resource($stream)) {
                                fclose($stream);
                            }
                        }

                        if (!Storage::disk('public')->exists($file)) {
                            throw new \RuntimeException("Local verification failed: {$file}");
                        }

                        if (Storage::disk('public')->size($file) !== Storage::disk('s3')->size($file)) {
                            throw new \RuntimeException("Local file size verification failed: {$file}");
                        }
                    }

                    if ($media->disk === 's3') {
                        $media->disk = 'public';
                    }

                    if ($media->conversions_disk === 's3') {
                        $media->conversions_disk = 'public';
                    }

                    $media->save();
                    $migrated++;
                    $this->line("[{$media->id}] migrated {$relativePath}");
                } catch (Throwable $exception) {
                    $failed++;
                    $this->error("[{$media->id}] {$exception->getMessage()}");
                }
            }
        });

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. No files or database records were changed.');
            return self::SUCCESS;
        }

        $this->info("Migration complete: {$migrated} migrated, {$failed} failed.");
        $this->comment('S3 originals were kept as a rollback-safe backup.');

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
