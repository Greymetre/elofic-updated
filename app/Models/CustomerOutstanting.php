<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOutstanting extends Model
{
    use HasFactory;

    protected $fillable = ['posting_date','customer_code','branch_id', 'customer_id', 'user_id', 'division_id','reference','customer_name','due_date', 'amount', 'days', 'year', 'quarter','payment_term', 'created_at', 'updated_at'];

    public $timestamps = true;

    public function distributor()
    {
        return $this->belongsTo(\App\Models\MasterDistributor::class, 'customer_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
