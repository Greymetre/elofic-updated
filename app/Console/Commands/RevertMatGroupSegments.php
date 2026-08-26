<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Subcategory;

/**
 * One-off cleanup: undoes the mat_group -> segment wiring done by sync:products,
 * putting every product back on the "All" segment and removing the segments the
 * sync created. Safe to delete once it has been run.
 */
class RevertMatGroupSegments extends Command
{
    protected $signature = 'segments:revert-matgroup {--dry-run : Report what would change without writing anything}';
    protected $description = 'Move all products back to the "All" segment and remove the segments created from mat_group';

    /** The 16 mat_group codes the sync turned into segments. */
    protected $matGroupCodes = [
        'PV', 'LU', 'EM', 'HC', 'AG', '2W', 'LC', 'CO',
        '3W', 'GN', 'PH', 'PC', 'CP', 'SE', 'CH', 'CC',
    ];

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $all = Subcategory::whereRaw('UPPER(TRIM(subcategory_name)) = ?', ['ALL'])->first();

        if (!$all) {
            $this->error('No "All" segment found — aborting, nothing to move products back to.');
            return 1;
        }

        $this->info('"All" segment id: ' . $all->id);

        $placeholders = implode(',', array_fill(0, count($this->matGroupCodes), '?'));

        $targets = Subcategory::where('id', '!=', $all->id)
            ->whereRaw('UPPER(TRIM(subcategory_name)) IN (' . $placeholders . ')', $this->matGroupCodes)
            ->get();

        if ($targets->isEmpty()) {
            $this->info('No mat_group segments found — nothing to revert.');
            return 0;
        }

        $targetIds = $targets->pluck('id')->all();

        $this->line('Segments to remove: ' . $targets->map(function ($s) {
            return $s->subcategory_name . ' (' . $s->id . ')';
        })->implode(', '));

        $productCount = Product::whereIn('subcategory_id', $targetIds)->count();

        $this->line('Products to move back to "All": ' . $productCount);

        if ($dryRun) {
            $this->warn('DRY RUN — nothing was written.');
            return 0;
        }

        DB::transaction(function () use ($targetIds, $all, $productCount) {

            Product::whereIn('subcategory_id', $targetIds)->update(['subcategory_id' => $all->id]);

            // refuse to drop a segment anything still points at
            $stillReferenced = Product::whereIn('subcategory_id', $targetIds)->count();
            if ($stillReferenced > 0) {
                throw new \RuntimeException($stillReferenced . ' products still reference the segments — rolled back.');
            }

            Subcategory::whereIn('id', $targetIds)->delete();

            \Log::info('Reverted mat_group segments: ' . $productCount . ' products moved back to "All" (' . $all->id . '), ' . count($targetIds) . ' segments deleted');
        });

        $this->info('Done — ' . $productCount . ' products moved back to "All", ' . count($targetIds) . ' segments deleted.');

        return 0;
    }
}
