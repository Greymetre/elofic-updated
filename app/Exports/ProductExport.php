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
    public function __construct($request)
    {
        $this->category_id = $request->input('category_id');
        $this->active = $request->input('active');
    }

    public function collection()
    {
        $data = Product::with('productpriceinfo')->select('id','active','product_name','product_code','new_group','sub_group','expiry_interval','expiry_interval_preiod', 'display_name', 'description', 'subcategory_id', 'category_id', 'brand_id', 'product_image', 'unit_id', 'specification', 'part_no','suc_del', 'product_no', 'model_no','phase','sap_code' , 'branch_id', 'hsn_sac', 'hsn_sac_no');
        if($this->category_id && !empty($this->category_id)){
            $data->where('category_id', $this->category_id);
        }
        if($this->active && !empty($this->active)){
            $data->where('active', $this->active);
        }
        $data = $data->latest()->get();

        return $data;
    }

    public function headings(): array
    {
            return ['Product id','Plant No','Material No','Part No','Material Group','Application','Material description','WM packing standad','Old Material','Material Type','Segment','Material Grp','Makers','Type','Model','Nishtha','Saathi','Pack Size','MRP','price','Remarks','Subcategory id','Category id','Category','Brand id','Product Image','status'];
   
    // return ['product_id','product_name','product_code','new_group','sub_group','expiry_interval','expiry_interval_preiod','display_name', 'description', 'subcategory_id','subcategory','category_id','category','brand_id','brand','product_image','unit_id','unit_name','mrp','price','selling_price','gst','discount','max_discount', 'hp', 'kw', 'product_stage', 'model_no','suc_del','Phase','status','Sap Code' , 'budget_for_month' , 'top_sku' , 'branch_id', 'rmc', 'hsn_sac', 'hsn_sac_no'];
    }

   public function map($data): array
{
    return [
        $data['id'] ?? '-',
        $data['branch_id'] ?? '-',
        $data['product_code'] ?? '-',
       
        $data['part_no'] ?? '-',
        $data['specification'] ?? '-',
        $data['phase'] ?? '-',
        $data['product_name'] ?? '-',
        $data['productpriceinfo']['rmc'] ?? '-',
        $data['product_no'] ?? '-',
         $data['sap_code'] ?? '-',
        
        $data['subcategories']['subcategory_name'] ?? '-',
        $data['description'] ?? '-',
        $data['brands']['brand_name'] ?? '-',
        
        $data['sub_group'] ?? '-',
        $data['model_no'] ?? '-',
        $data['productpriceinfo']['budget_for_month'] ?? '-',
        $data['suc_del'] ?? '-',
        
        $data['hsn_sac_no'] ?? '-',
        $data['productpriceinfo']['mrp'] ?? '-',
        $data['productpriceinfo']['price'] ?? '-',
        $data['productpriceinfo']['top_sku'] ?? '-',

        $data['subcategory_id'] ?? '-',
        $data['category_id'] ?? '-',
        $data['categories']['category_name'] ?? '-',
        $data['brand_id'] ?? '-',
        $data['product_image'] ?? '-',
        $data['active'] ?? '-',

        // $data['new_group'] ?? '',
        
        // $data['expiry_interval'] ?? '',
        // $data['expiry_interval_preiod'] ?? '',
        // $data['display_name'] ?? '',
        

        

        

        
        

        

        // $data['unit_id'] ?? '',
        // $data['unitmeasures']['unit_name'] ?? '',

        
        // $data['productpriceinfo']['selling_price'] ?? '',
        // $data['productpriceinfo']['gst'] ?? '',
        // $data['productpriceinfo']['discount'] ?? '',
        // $data['productpriceinfo']['max_discount'] ?? '',

        
        
        
        

        
       
       
        

        
        

        // $data['branch_id'] ?? '',

        

        // $data['hsn_sac'] ?? '',
        
    ];
}

}
