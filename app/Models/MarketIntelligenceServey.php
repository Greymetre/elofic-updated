<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MarketIntelligenceServey extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $table = 'market_intelligence_serveys';

    protected $fillable = [
        'state_id',
        'division_id',
        'category_id',
        'brand_id',
        'product_name',
        'cooling_arrangement_id',
        'type_of_construction_id',
        'hp_id',
        'stage',
        'phase_id',
        'head_range_mtr',
        'discharge_range_lpm',
        'sucx_del',
        'speed',
        'mrp',
        'list_price',
        'landed_to_dealers',
        'remark',
        'created_by',
        'crated_at',
        'updated_at'
    ];

    public function createdbyname()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id')->select('id','name');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state_id', 'id')->select('id','state_name');
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('servey_image')
             ->useFallbackUrl(asset(config('constants.NO_IMAGE_URL')))
             ->useFallbackPath(public_path(config('constants.NO_IMAGE_URL')))
             ->singleFile();
    }


}
