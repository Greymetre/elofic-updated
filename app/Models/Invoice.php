<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    protected $fillable = [
        'customer_id',
        'place_of_supply',
        'invoice_no',
        'order_no',
        'invoice_date',
        'payment_term',
        'due_date',
        'user_id',
        'sub_total',
        'discount_type',
        'discount',
        'discount_amount',
        'tds',
        'tds_amount',
        'adjustment',
        'grand_total',
        'customer_notes',
        't_c'
    ];

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoice_files');
    }
}
