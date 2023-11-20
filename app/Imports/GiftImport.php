<?php

namespace App\Imports;

use App\Models\Gifts;
use Maatwebsite\Excel\Concerns\ToModel;

class GiftImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Gifts([
            //
        ]);
    }
}
