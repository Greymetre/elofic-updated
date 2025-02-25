<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlannedSOP extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'plan_next_month',
        'opening_stock',
        'budget_for_month',
        'last_month_sale',
        'last_three_month_avg',
        'last_year_month_sale',
        'sku_unit_price',
        's_op_val',
        'top_sku',
        'created_by',
    ];

    public function getProduct(){
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function getBranch(){
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }
}
