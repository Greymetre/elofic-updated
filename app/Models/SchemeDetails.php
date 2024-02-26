<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeDetails extends Model
{
    use HasFactory;

    protected $table = 'scheme_details';

    protected $fillable = [ 'active', 'scheme_id', 'product_id', 'category_id', 'subcategory_id', 'minimum', 'maximum', 'points', 'created_at', 'updated_at'];

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->select('id','product_name','display_name','product_image');
    }
    public function categories()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id')->select('id','category_name','category_image');
    }
    public function subcategories()
    {
        return $this->belongsTo('App\Models\Subcategory', 'subcategory_id', 'id')->select('id','subcategory_name','subcategory_image');
    }
}
