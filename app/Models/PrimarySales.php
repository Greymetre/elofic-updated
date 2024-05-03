<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrimarySales extends Model
{
    use HasFactory;

    protected $table = 'primary_sales';

    protected $fillable = [ 
        'active','invoiceno','invoice_date','month','division','dealer','branch','city','state','final_branch','sales_person','product_name','quantity','rate','net_amount','tax_amount','cgst_amount','sgst_amount','igst_amount','total_amount','new_group','store_name','group_name','new_group_name','product_id','created_at','updated_at'];

}
