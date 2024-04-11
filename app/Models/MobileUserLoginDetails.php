<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileUserLoginDetails extends Model
{
    use HasFactory;

    protected $table = 'mobile_user_login_details';

    protected $fillable = [
        'active', 
        'customer_id', 
        'app_version', 
        'device_type', 
        'device_name', 
        'first_login_date', 
        'last_login_date',
        'login_status',
        'created_at', 
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id', 'id');
    }
}
