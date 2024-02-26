<?php

namespace App\DataTables;

use App\Models\Customers;
use App\Models\ParentDetail;
use App\Models\SchemeDetails;
use App\Models\TransactionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class TransactionHistoryDataTable extends DataTable
{

    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('contact_person', function ($data) {
                $employee = '';
                if (!empty($data->customer->getemployeedetail)) {
                    foreach ($data->customer->getemployeedetail as $key_new => $datas) {
                        if ($key_new == (count($data->customer->getemployeedetail) - 1)) {
                            $employee .= isset($datas->employee_detail->name) ? $datas->employee_detail->name : '';
                        } else {
                            $employee .= isset($datas->employee_detail->name) ? $datas->employee_detail->name . ', ' : '';
                        }
                    }
                }
                return $employee;
            })
            ->editColumn('customer.name', function ($data) {
                $customer_name = '<a href="'.route('customers.edit', [encrypt($data->customer->id)]).'">'.$data->customer->name.'</a>';
                
                return $customer_name;
            })
            ->editColumn('parent_name', function ($data) {
                $parents = '';
                if (!empty($data->customer->getparentdetail)) {
                    foreach ($data->customer->getparentdetail as $key => $parent_data) {
                        if ($key == (count($data->customer->getemployeedetail) - 1)) {
                            $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                        } else {
                            $parents .= isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name . ', ' : '';
                        }
                    }
                }
                return $parents;
            })
            ->editColumn('subcategory_name', function ($data) {

                return $data->scheme->product->subcategories->subcategory_name;
            })
            ->editColumn('product_name', function ($data) {

                return $data->scheme->product->product_name;
            })
            ->editColumn('points', function ($data) {
                $scheme_details = SchemeDetails::where('product_id', $data->scheme->product->id)->first();
                return ($scheme_details) ? $scheme_details->points: '';
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['scheme_delete'])) {
                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' Transaction History">
                                <i class="material-icons">clear</i>
                              </a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>' . $activebtn;
            })
            ->rawColumns(['action', 'contact_person', 'parent_name', 'subcategory_name', 'product_name', 'points','customer.name']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Scheme $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TransactionHistory $model, Request $request)
    {
        
        $data = $model->with('customer', 'scheme');
        if($request->branch_id && $request->branch_id != null && count($request->branch_id) > 0){
            $branch_user_id = User::whereIn('branch_id',$request['branch_id'])->pluck('id');
            if(!empty($branch_user_id)){
                $branch_customer_id = Customers::whereIn('executive_id',$branch_user_id)->pluck('id');
            }
            if(!empty($branch_customer_id)){
                $data->whereIn('customer_id', $branch_customer_id);
            }
        }
        if($request->parent_customer && $request->parent_customer != null  && count($request->parent_customer) > 0){
            $parent_customer_id = ParentDetail::whereIn('parent_id',$request->parent_customer)->pluck('customer_id');
            
            if(!empty($parent_customer_id)){
                $data->whereIn('customer_id', $parent_customer_id);
            }
        }
        if($request->start_date && $request->start_date != null && $request->start_date != '' && $request->end_date && $request->end_date != null && $request->end_date != ''){
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate = date('Y-m-d', strtotime($request->end_date));
            $data = $data->whereDate('created_at', '>=', $startDate)
             ->whereDate('created_at', '<=', $endDate);
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
            ->setTableId('getTransactionHistory')
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
