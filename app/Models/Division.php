<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [ 'active', 'division_name','created_by', 'updated_by', 'deleted_at', 'created_at', 'updated_at'];

}
