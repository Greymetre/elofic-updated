<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MspActivity extends Model
{
    use HasFactory;

    protected $table = 'msp_activities';

    protected $fillable = [
        'emp_code',
        'fyear',
        'month',
        'msp_count',
        'created_at',
        'updated_at',
        'activity_type'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'emp_code', 'employee_codes');
    }
}
