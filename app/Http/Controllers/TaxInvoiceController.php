<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxInvoiceController extends Controller
{
    public function index()
    {
        return view('taxinvoice.index');
    }
}