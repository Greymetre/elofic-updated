<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Subcategory;

class SyncProducts extends Command
{
    protected $signature = 'sync:products';
    protected $description = 'Sync products from external API';

    public function handle()
    {
        $created = 0;
        $updated = 0;
        $unchanged = 0;

        // Products without a segment are mapped to the "All" segment so the app can show/filter them.
        $defaultSubcategoryId = Subcategory::where('subcategory_name', 'All')->where('active', 'Y')->value('id');

        $response = Http::get('http://103.25.175.214:9297/api/catelogdata');

        if (!$response->successful()) {
            Log::channel('product_sync')->error('Product sync API failed', [
                'status' => $response->status(),
            ]);
            return;
        }

        $products = $response->json()['data']; // adjust if nested

        foreach ($products as $item) {

            // 🔹 UNIQUE FIELD (important)
            $productCode = $item['mat_number'] ?? null;

            // 🔹 FIND OR NEW
            $product = Product::firstOrNew([
                'product_code' => $productCode
            ]);
            $isNewProduct = !$product->exists;

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
                // API creation date -> drives automatic NPD tag (npd_excluded is never touched by sync)
                'launch_date' => $this->parseEntryDate($item['entry_date'] ?? null) ?? $product->launch_date,
                
                // 'hsn_sac' => $item['hsn_sac'] ?? null,
                // 'product_image' => $item['image'] ?? null,
                'active' => "Y"
            ]);

            if (empty($product->subcategory_id) && $defaultSubcategoryId) {
                $product->subcategory_id = $defaultSubcategoryId;
            }

            $productChanges = array_keys($product->getDirty());
            if (!empty($productChanges)) {
                $product->save();
            }

            // =========================
            // 🔥 PRODUCT DETAILS
            // =========================

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

                    $detailChanges = array_keys($productDetail->getDirty());
                    if (!empty($detailChanges)) {
                        $productDetail->save();
                    }

                    if ($isNewProduct) {
                        $created++;
                        $action = 'created';
                    } elseif (!empty($productChanges) || !empty($detailChanges)) {
                        $updated++;
                        $action = 'updated';
                    } else {
                        $unchanged++;
                        continue;
                    }

                    Log::channel('product_sync')->info('Product sync item', [
                        'action' => $action,
                        'product_id' => $product->id,
                        'product_code' => $product->product_code,
                        'product_name' => $product->product_name,
                        'product_fields' => $productChanges,
                        'detail_fields' => $detailChanges,
                    ]);
                // }
            // }
        }

            Log::channel('product_sync')->info('Product sync completed', [
                'received' => count($products),
                'created' => $created,
                'updated' => $updated,
                'unchanged' => $unchanged,
            ]);
        }

    /** Parse the API entry_date (e.g. "2025-07-28 22:38:51"); null for empty / zero / invalid values. */
    private function parseEntryDate($value): ?\Carbon\Carbon
    {
        $value = trim((string) $value);
        if ($value === '' || str_starts_with($value, '0000-00-00')) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Throwable $e) {
            Log::channel('product_sync')->warning('Invalid entry_date from product API', ['entry_date' => $value]);
            return null;
        }
    }
}
