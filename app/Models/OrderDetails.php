<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [ 'active', 'order_id', 'product_id', 'product_detail_id','quantity', 'shipped_qty', 'price', 'tax_amount', 'line_total', 'status_id', 'created_at', 'updated_at','ebd_discount','ebd_name','ebd_amount','cluster_discount','cluster_amount','distributor_discount','distributor_amount','deal_discount','deal_amount'];

    public function products()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id')->select('id','product_name','display_name','product_image', 'specification', 'part_no', 'product_no', 'model_no','category_id','subcategory_id','suc_del');
    }

    public function orders()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function productdetails()
    {
        return $this->belongsTo('App\Models\ProductDetails', 'product_detail_id', 'id')->select('id','detail_title','gst');
    }

    public function statusname()
    {
        return $this->belongsTo('App\Models\Status', 'status_id', 'id')->select('id','status_name');
    }
}
