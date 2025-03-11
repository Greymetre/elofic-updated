<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpeningStock extends Model
{
    use HasFactory;

    protected $fillable = [
         'product_id', 'ware_houses_id' , 'branch_id' , 'opening_stocks'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'ware_houses_id');
    }
}
