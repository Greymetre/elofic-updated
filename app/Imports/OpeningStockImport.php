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
    protected $file;

    public function __construct($file = null)
    {
        $this->file = $file;
        // dd('File received in constructor', $file); // REMOVE THIS to allow collection() to run
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row['itm_code']) && isset($row['branch_id'])) {
               $stock =OpeningStock::updateOrCreate(
                    [
                        'item_code' => $row['itm_code'],
                        'branch_id'  => $row['branch_id'],
                        'item_description'       => $row['itm_desc'] ?? null,
                        'item_group'       => $row['itm_grp_name'] ?? null,
                    ],
                    [
                        'ware_house_name' => $row['warehouse_name'] ?? null,  // Use ternary instead of isset()
                        'opening_stocks' => $row['instock_qty'] ?? 0,             // Use null coalescing operator
                        'open_order_qty'    => $row['opening_qty'] ?? 0
                    ]
                );
            }
        }
    }

    public function rules(): array
    {
        return [
            // 'itm_code' => 'nullable|string|sometimes',
            'itm_desc' => 'nullable|string',
            'itm_grp_name' => 'nullable|string',
            'warehouse_name' => 'nullable|string',
            'branch_id' => 'nullable|integer',
            'instock_qty' => 'nullable|numeric',
            'opening_qty' => 'nullable|numeric',
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
