<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $fillable = [ 'expenses_type', 'date', 'claim_amount', 'start_km', 'stop_km', 'total_km','note','attechment', 'created_at', 'updated_at'];

    public function expense_type(){
        return $this->hasOne(ExpensesType::class);
    }
}
