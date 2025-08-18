<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'gst',
        'gst_no',
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
        'tds',
        'tds_amount',
        't_c',
        'grand_total',
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
}
