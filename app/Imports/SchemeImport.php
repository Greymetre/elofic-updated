<?php

namespace App\Imports;

use App\Models\Scheme;
use App\Models\SchemeDetails;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Support\Facades\DB;
use Log;

use Illuminate\Support\Facades\Auth;

class SchemeImport implements ToCollection,WithValidation,WithHeadingRow, WithBatchInserts , WithChunkReading
{
    use Importable, SkipsFailures;

    
    
    public function model(array $row)
    {
        return new Scheme([
            //
        ]);
    }
    public function collection(Collection $rows)
    {
        $schemedetails = collect([]);
        foreach ($rows as $row) {
            if( $scheme = Scheme::create([
                
                'active' => 'Y',
                'scheme_name' => isset($row['scheme_name'])? ucfirst($row['scheme_name']):'',
                'scheme_description' => isset($row['scheme_description'])? ucfirst($row['scheme_description']):'',
                'start_date' => isset($row['start_date'])? $row['start_date']:null,
                'end_date' => isset($row['end_date'])? $row['end_date']:null,
                'points_start_date' => isset($row['points_start_date'])? $row['points_start_date']:null,
                'points_end_date' => isset($row['points_end_date'])? $row['points_end_date']:null,
                'block_points' => isset($row['block_points'])? $row['block_points']:null,
                'block_percents' => isset($row['block_percents'])? $row['block_percents']:null,
                'scheme_image' => isset($row['scheme_image'])? $row['scheme_image']:'',
                'scheme_type' => isset($row['scheme_type'])? $row['scheme_type']:'',
                'point_value' => isset($row['point_value'])? $row['point_value']:0.00,
                'regions' => isset($row['regions'])? $row['regions']:null,
                'created_at' => getcurentDateTime() ,
                'updated_at' => getcurentDateTime()
            ]) )
            {

               $schemedetails->push([
                    
                    'active' => 'Y',
                    'scheme_id' => $scheme['id'],
                    'product_id' => isset($row['product_id'])? $row['product_id']:null,
                    'category_id' => isset($row['category_id'])? $row['category_id']:null,
                    'subcategory_id' => isset($row['subcategory_id'])? $row['subcategory_id']:null,
                    'minimum' => isset($row['minimum'])? $row['minimum']:null,
                    'maximum' => isset($row['maximum'])? $row['maximum']:null,
                    'points' => isset($row['points'])? $row['points']:null,
                    'created_at' => getcurentDateTime() ,
                    'updated_at' => getcurentDateTime()
                ]);
            }
        }
        if($schemedetails->isNotEmpty())
        {
            SchemeDetails::insert($schemedetails->toArray());
        }
    }
    public function rules(): array
    {
        return [
            'scheme_name' => 'required|string|regex:/[a-zA-Z0-9\s]+/',
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
