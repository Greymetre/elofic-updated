<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimGenerationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_generation_id' , 'complaint_id'
    ];
}
