<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\ProductDetails;

class SyncProducts extends Command
{
    protected $signature = 'sync:products';
    protected $description = 'Sync products from external API';

    public function handle()
    {
        $response = Http::get('http://103.25.175.214:9297/api/catelogdata');

        if (!$response->successful()) {
            \Log::error('API Failed');
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
                
                // 'hsn_sac' => $item['hsn_sac'] ?? null,
                // 'product_image' => $item['image'] ?? null,
                'active' => "Y"
            ]);

            // 🔥 SAVE ONLY IF CHANGED
            if ($product->isDirty()) {
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

                    if ($productDetail->isDirty()) {
                        $productDetail->save();
                    }
                // }
            // }
        }

            \Log::info('Product Sync Completed');
        }
    }