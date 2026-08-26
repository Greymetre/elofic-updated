<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Subcategory;

class SyncProducts extends Command
{
    protected $signature = 'sync:products {--dry-run : Report what would change without writing anything}';
    protected $description = 'Sync products from external API';

    /**
     * mat_group (uppercased) => subcategories.id
     * @var array<string,int>
     */
    protected $segments = [];

    /**
     * subcategories.id of the catch-all "All" segment, used when mat_group is blank.
     * @var int|null
     */
    protected $allSegmentId = null;

    /** @var bool */
    protected $segmentHasSapCode = false;

    /** @var bool */
    protected $dryRun = false;

    protected $stats = [
        'segments_created' => 0,
        'segments_matched' => 0,
        'products_wired'   => 0,
        'wired_to_all'     => 0,
    ];

    public function handle()
    {
        $this->dryRun = (bool) $this->option('dry-run');

        $response = Http::get('http://103.25.175.214:9297/api/catelogdata');

        if (!$response->successful()) {
            \Log::error('API Failed');
            return;
        }

        $products = $response->json()['data']; // adjust if nested

        $this->loadSegments();

        foreach ($products as $item) {

            // 🔹 UNIQUE FIELD (important)
            $productCode = $item['mat_number'] ?? null;

            // 🔹 FIND OR NEW
            $product = Product::firstOrNew([
                'product_code' => $productCode
            ]);

            // 🔹 SEGMENT (subcategory) FROM mat_group
            $subcategoryId = $this->resolveSegmentId($item['mat_group'] ?? null);

            // 🔹 MAP DATA
            $product->fill([
                // 'product_no' => $item['product_no']??"",
                'product_code' => $item['mat_number'] ?? null,
                // 'sap_code' => $item['mat_number'] ?? null,
                'hsn_sac' => $item['kbetr'] ?? 0,
                'product_name' => $item['mat_description'] ?? null,
                'description' => $item['mat_description'] ?? null,
                'hsn_sac_no' => $item['zwmpack'] ?? null,
                'suc_del' => $item['zcvalue'] ?? null,
                'budget_for_month' => $item['znpoint'] ?? null,
                'product_no' => $item['prodid'] ?? '',
                'subcategory_id' => $subcategoryId,

                // 'hsn_sac' => $item['hsn_sac'] ?? null,
                // 'product_image' => $item['image'] ?? null,
                'active' => "Y"
            ]);

            if ($product->isDirty('subcategory_id')) {
                $this->stats['products_wired']++;
            }

            if ($subcategoryId && $subcategoryId === $this->allSegmentId) {
                $this->stats['wired_to_all']++;
            }

            // 🔥 SAVE ONLY IF CHANGED
            if ($product->isDirty() && !$this->dryRun) {
                $product->save();
            }

            // =========================
            // 🔥 PRODUCT DETAILS
            // =========================

            // dry-run cannot build details for a product that was never persisted
            if ($this->dryRun && !$product->exists) {
                continue;
            }

            // if (!empty($item['details'])) {

                // foreach ($item['details'] as $detail) {

                    $productDetail = ProductDetails::firstOrNew([
                        'product_id' => $product->id,
                        // 'ean_code' => $item['ean_code'] ?? null
                    ]);

                    $productDetail->fill([
                        'detail_title' => $item['mat_description'] ?? '',
                        'budget_for_month' => $item['znpoint'] ?? null,
                        'mrp' => $item['kbetr'] ?? 0,
                        'price' => $item['kbetr'] ?? 0,
                        // 'selling_price' => $item['selling_price'] ?? 0,
                        // 'gst' => $item['gst'] ?? 0,
                        // 'stock_qty' => $item['stock'] ?? 0,
                        // 'isprimary' => $item['is_primary'] ?? 0,
                    ]);

                    if ($productDetail->isDirty() && !$this->dryRun) {
                        $productDetail->save();
                    }
                // }
            // }
        }

        $this->reportStats();

        \Log::info('Product Sync Completed');
    }

    /**
     * Cache every existing segment so a mat_group can be matched without a query per row.
     * Inactive segments are included too, otherwise the sync would create duplicates of them.
     */
    protected function loadSegments()
    {
        $this->segmentHasSapCode = Schema::hasColumn('subcategories', 'sap_code');

        $columns = ['id', 'subcategory_name'];
        if ($this->segmentHasSapCode) {
            $columns[] = 'sap_code';
        }

        foreach (Subcategory::select($columns)->get() as $segment) {

            $name = strtoupper(trim((string) $segment->subcategory_name));

            if ($name === 'ALL') {
                $this->allSegmentId = $segment->id;
                continue; // "All" is the fallback bucket, never a mat_group match
            }

            if ($name !== '') {
                $this->segments[$name] = $segment->id;
            }

            if ($this->segmentHasSapCode) {
                $code = strtoupper(trim((string) $segment->sap_code));
                if ($code !== '') {
                    $this->segments[$code] = $segment->id;
                }
            }
        }

        if (!$this->allSegmentId) {
            \Log::warning('Product Sync: no "All" segment found, blank mat_group rows will be left unwired');
        }
    }

    /**
     * mat_group -> subcategories.id, creating the segment the first time a group is seen.
     * Blank mat_group falls back to the "All" segment.
     */
    protected function resolveSegmentId($matGroup)
    {
        $group = strtoupper(trim((string) $matGroup));

        if ($group === '') {
            return $this->allSegmentId;
        }

        if (isset($this->segments[$group])) {
            $this->stats['segments_matched']++;
            return $this->segments[$group];
        }

        $this->stats['segments_created']++;

        if ($this->dryRun) {
            $this->segments[$group] = 0; // placeholder so the same group is not counted twice
            return null;
        }

        $attributes = [
            'active'             => 'Y',
            'ranking'            => 1,
            'subcategory_name'   => $group,
            'subcategory_image'  => '',
            'category_id'        => $this->defaultCategoryId(),
        ];

        if ($this->segmentHasSapCode) {
            $attributes['sap_code'] = $group;
        }

        $segment = Subcategory::create($attributes);

        \Log::info('Product Sync: created segment "' . $group . '" (id ' . $segment->id . ')');

        return $this->segments[$group] = $segment->id;
    }

    /**
     * New segments sit under the same category as the existing ones.
     */
    protected function defaultCategoryId()
    {
        static $categoryId;

        if ($categoryId === null) {
            $categoryId = Subcategory::whereNotNull('category_id')->value('category_id') ?: 1;
        }

        return $categoryId;
    }

    protected function reportStats()
    {
        $prefix = $this->dryRun ? 'Product Sync [DRY RUN]' : 'Product Sync';

        $line = $prefix . ': segments created=' . $this->stats['segments_created']
            . ', segments matched=' . $this->stats['segments_matched']
            . ', products wired to a segment=' . $this->stats['products_wired']
            . ', of which fell back to "All"=' . $this->stats['wired_to_all'];

        $this->info($line);
        \Log::info($line);
    }
}
