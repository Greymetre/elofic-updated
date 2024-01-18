<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gifts extends Model
{
    use HasFactory;

    protected $table = 'gifts';

    protected $fillable = [ 'active', 'product_name', 'display_name', 'description', 'product_image', 'mrp', 'price', 'points', 'subcategory_id', 'category_id', 'brand_id', 'unit_id', 'created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

   public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id')->select('id','category_name','category_image');
    }

    public function subcategories()
    {
        return $this->belongsTo('App\Models\Subcategory', 'subcategory_id', 'id')->select('id','subcategory_name','subcategory_image');
    }

    public function brands()
    {
        return $this->belongsTo('App\Models\Brand', 'brand_id', 'id')->select('id','brand_name','brand_image');
    }

    public function unitmeasures()
    {
        return $this->belongsTo('App\Models\UnitMeasure', 'unit_id', 'id')->select('id','unit_name','unit_code');
    }
    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }
}
