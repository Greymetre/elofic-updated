<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\WareHouse;
use App\Models\OpeningStock;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class OpeningStockImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $product = Product::where('product_code', $row['itm_code'])->first();
            $warehouse = WareHouse::where('warehouse_name', $row['warehouse_name'])->first();
            if ($product && $warehouse && isset($row['branch_id'])) {
                OpeningStock::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'branch_id'  => $row['branch_id'],
                    ],
                    [
                        'ware_houses_id' => $warehouse->id,
                        'opening_stocks' => isset($row['instock_qty']) ? $row['instock_qty'] : 0
                    ]
                );
            }
        }
    }

    public function rules(): array
    {
        return [
            'itm_code' => 'required|string',
            'itm_desc' => 'required|string',
            'itm_grp_name' => 'required|string',
            'warehouse_name' => 'required|string',
            'branch_id' => 'required|integer',
            'instock_qty' => 'required|numeric',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function onFailure(Failure ...$failures)
    {
        Log::stack(['import-failure-logs'])->info(json_encode($failures));
    }
}
