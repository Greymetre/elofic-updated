<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table = 'user_details';
    
    protected $fillable = ['active', 'user_id', 'date_of_birth', 'date_of_joining', 'marital_status', 'deleted_at', 'created_at', 'updated_at','salary','last_year_increments','last_promotion', 'order_mails', 'order_mails_type']; 
}
