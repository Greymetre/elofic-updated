<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ProductExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Product::with('productpriceinfo')->select('id','product_name', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no', 'product_no', 'model_no')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','product_name', 'display_name', 'description', 'subcategory_id','subcategory','category_id','category','brand_id','brand','product_image', 'unit_id','unit_name','mrp','price','selling_price','gst','hsn_code','ean_code','discount','max_discount', 'specification', 'part_no', 'product_no', 'model_no'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['product_name'],
            $data['display_name'],
            $data['description'],
            $data['subcategory_id'],
            $data['subcategories']['subcategory_name'],
            $data['category_id'],
            $data['categories']['category_name'],
            $data['brand_id'],
            $data['brands']['brand_name'],
            $data['product_image'],
            $data['unit_id'],
            $data['unitmeasures']['unit_name'],
            isset($data['productpriceinfo']['mrp']) ? $data['productpriceinfo']['mrp'] :'',
            isset($data['productpriceinfo']['price']) ? $data['productpriceinfo']['price'] :'',
            isset($data['productpriceinfo']['selling_price']) ? $data['productpriceinfo']['selling_price'] :'',
            isset($data['productpriceinfo']['gst']) ? $data['productpriceinfo']['gst'] : '',
            isset($data['productpriceinfo']['hsn_code']) ? $data['productpriceinfo']['hsn_code'] :'',
            isset($data['productpriceinfo']['ean_code']) ? $data['productpriceinfo']['ean_code'] : '',
            isset($data['productpriceinfo']['discount']) ? $data['productpriceinfo']['discount'] : '',
            isset($data['productpriceinfo']['max_discount']) ? $data['productpriceinfo']['max_discount'] :'' ,
            isset($data['specification']) ? $data['specification'] :'',
            isset($data['part_no']) ? $data['part_no'] :'',
            isset($data['product_no']) ? $data['product_no'] :'',            
            isset($data['model_no']) ? $data['model_no'] :'',
        ];
    }

}