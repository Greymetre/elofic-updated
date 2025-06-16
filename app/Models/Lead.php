<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_url',
        'address_id',
        'status',
        'assign_to',
        'created_by',
    ];

    
    public function contacts(){
        return $this->hasMany(LeadContact::class);
    }

    public function notes(){
        return $this->hasMany(LeadNote::class);
    }
}
