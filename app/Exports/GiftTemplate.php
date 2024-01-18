<?php

namespace App\Exports;

use App\Models\Gifts;
use Maatwebsite\Excel\Concerns\FromCollection;

class GiftTemplate implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Gifts::all();
    }
}
