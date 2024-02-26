<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Expenses extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [ 'expenses_type','user_id', 'date', 'claim_amount', 'start_km', 'stop_km', 'total_km','note','checker_status','accountant_status','created_at', 'updated_at','created_by'];

    // public function expense_type(){
    //     return $this->hasOne(ExpensesType::class);
    // }


   public function expense_type(){
        return $this->belongsTo(ExpensesType::class,'expenses_type','id');
    }

   public function users(){
        return $this->belongsTo(User::class,'user_id','id');
    }

  public function registerMediaCollections(): void {
        $this->addMediaCollection('expense_file')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }



}
