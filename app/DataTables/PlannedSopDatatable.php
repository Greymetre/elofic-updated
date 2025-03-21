<?php

namespace App\DataTables;

use App\Models\PlannedSOP;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlannedSopDatatable extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('status' , function($data){
                 if($data->status == 1){
                    return '<span class="badge badge-success">OPEN</span>';
                 }else if(isset($data->status) && $data->status == "0"){
                    return '<span class="badge badge-danger">CANCEL</span>';
                 }else{
                     return '<span class="badge badge-dager">CANCEL</span>';
                 } 
            })
            ->editColumn('planning_month' , function($data){
                 try{
                    if(isset($data->planning_month)){
                        return \Carbon\Carbon::parse($data->planning_month)->format('F Y');
                    }else{
                         return "Not Found";
                    }
                 }catch(\Exception $e){
                    return "Not Found";
                 }
            })
             ->addColumn('action', function ($query) {
                  $btn = '';
                  $activebtn ='';
                  if(auth()->user()->can(['sop_edit']))
                  {
                    $btn = $btn.'<a href="'.route('planned-sop.edit', encrypt($query->id)).'" class="btn btn-info btn-just-icon btn-sm edit mr-2" id="'.encrypt($query->id).'" title="'.trans('panel.global.edit').' SOP">
                          <i class="material-icons">edit</i>
                        </a>';
                  }
                  if (auth()->user()->can(['sop_cancel'])) {
                        $btn .= '<form action="' . route('planned-sop.update', encrypt($query->id)) . '" method="POST" class="update-form-' .$query->id . '" style="display:inline;">
                                    ' . csrf_field() . '
                                    ' . method_field('PUT') . '
                                    <input type="hidden" name="status" value="0">
                                    <button type="button" class="btn btn-warning btn-just-icon btn-sm update-sop mr-2" data-id="' . $query->id . '" title="Cancel SOP">
                                        <i class="material-icons">close</i>
                                    </button>
                                </form>';
                    }

                    if (auth()->user()->can(['sop_delete'])) {
                        $btn .= '<form action="' . route('planned-sop.destroy', encrypt($query->id)) . '" method="POST" class="delete-form-' .$query->id . '" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '

                                <button type="button" class="btn btn-danger btn-just-icon btn-sm delete-sop " data-id="' . $query->id . '" title="Delete SOP">
                                    <i class="material-icons">delete</i>
                                </button>
                            </form>';
                    }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })            
            ->rawColumns(['action' , 'status' , 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PlannedSOP $model, Request $request)
    {
        $filters = $request->all();
        $data = $model->with(['getProduct.subcategories' , 'getProduct.categories', 'getBranch']);

        foreach ($filters as $key => $value) {
             if (isset($value))  {
                switch ($key) {
                    case "created_by" : 
                    case 'top_sku':
                        $data->where($key, 'like', "%$value%");
                        break;
                    case "product_name" :
                    case "description": 
                    case "product_code":
                        $data->whereHas('getProduct', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "branch_name":
                        $data->whereHas('getBranch', function ($q) use ($value , $key) {
                            $q->where($key, 'like', "%$value%");
                        });
                        break;
                    case "category_name" : 
                        $data->whereHas('getProduct.categories', function ($q) use ($value) {
                            $q->where('category_name', 'like', "%$value%");
                        });
                        break;
                    case "group_name" : 
                        $data->whereHas('getProduct.subcategories', function ($q) use ($value) {
                            $q->where('subcategory_name', 'like', "%$value%");
                        });
                        break;
                    case 'plan_next_month':
                    case 'budget_for_month':
                    case 'last_month_sale':
                    case 'last_three_month_avg':
                    case 'last_year_month_sale':
                    case 'sku_unit_price':
                    case 's_op_val':
                    case  'status' :
                    case 'order_id': 
                     $data->where($key, 'like', "$value");
                     break;
                }
            }
        }

        if(isset($request->planning_month)){
          try{
            $formatted_date = Carbon::createFromFormat('F Y', $request->planning_month)->startOfMonth();
            $planning_month = $formatted_date->format("Y-m-d");
            $data->whereDate('planning_month' , $planning_month);
          }catch(\Exception $e){
             $data = $data->latest()->newQuery();
          }
        }
        $data = $data->latest()->newQuery();
        return $data;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('product-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }
}
