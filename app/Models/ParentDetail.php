<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentDetail extends Model
{
    use HasFactory;
    protected $table = 'parent_details';

    protected $fillable = [ 'active', 'customer_id','parent_id','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];



    public function parent_detail()
    {
        return $this->belongsTo('App\Models\Customers', 'parent_id', 'id');
    }
}
