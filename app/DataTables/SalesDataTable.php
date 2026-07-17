<?php

namespace App\DataTables;

use App\Models\Order;
use App\Models\Sales;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class SalesDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('invoice_date', function ($data) {
                return isset($data->invoice_date) ? showdateformat($data->invoice_date) : '';
            })
            ->addColumn('buyer_name', function ($data) {
                $order = $data->orders?->resolveCustomerRelations();
                return $order?->buyers?->shop_name ?? $order?->buyers?->trade_name ?? $order?->buyers?->legal_name ?? '-';
            })
            ->addColumn('seller_name', function ($data) {
                $order = $data->orders?->resolveCustomerRelations();
                return $order?->sellers?->shop_name ?? $order?->sellers?->trade_name ?? $order?->sellers?->legal_name ?? '-';
            })
            ->addColumn('customer_type', fn ($data) => strtoupper((string) ($data->orders?->customer_type ?? '-')))
            ->addColumn('dispatch_status', fn ($data) => $data->orders?->dispatch_status ?? 'Pending')
            
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['sale_edit'])) {
                    $btn = $btn . '<a href="' . url("sales/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.sale.title_singular') . '">
                                    <i class="material-icons">edit</i>
                                </a>';
                }
                if (auth()->user()->can(['sale_show'])) {
                    $btn = $btn . '<a href="' . url("sales/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.sale.title_singular') . '">
                                    <i class="material-icons">visibility</i>
                                </a>';
                }
                if (auth()->user()->can(['sale_active'])) {
                    $approve = ($query->status_id == 6 || $query->status_id == 7) ? '<a href="' . url("saleApproval/" . encrypt($query->id)) . '" class="btn btn-sm btn-just-icon">
                                    <i class="material-icons">check_circle_outline</i>
                                </a>' : '';
                    $btn = $btn . $approve;
                }
                if (auth()->user()->can(['sale_delete'])) {
                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.sale.title_singular') . '">
                                <i class="material-icons">clear</i>
                              </a>';
                }
                if (auth()->user()->can(['sale_active'])) {
                    $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    $activebtn = '<div class="togglebutton">
                                <label>
                                  <input type="checkbox"' . $active . ' id="' . $query->id . '" class="activeRecord">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>' . $activebtn;
            })
            ->rawColumns(['action']);
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Sale $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Sales $model, Request $request)
    {
        $userids = getUsersReportingToAuth();
        $query = $model->with('createdbyname', 'orders.orderdetails')->whereHas('orders', function ($query) use ($userids) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Sub billing')) {
                $query->where(function ($subQuery) use ($userids) {
                    $subQuery->whereIn('executive_id', $userids)
                        ->orWhereIn('created_by', $userids);
                });
            }
        });

        if ($request->filled('customer_type_id')) {
            $query->whereHas('orders', fn ($orderQuery) => $orderQuery
                ->where('customer_type', strtoupper((string) $request->customer_type_id)));
        }

        if ($request->filled('retailers_id')) {
            $query->whereHas('orders', fn ($orderQuery) => $orderQuery->where('buyer_id', $request->retailers_id));
        }

        if ($request->filled('distributor_id')) {
            $query->whereHas('orders', fn ($orderQuery) => $orderQuery->where('seller_id', $request->distributor_id));
        }

        if ($request->dividion_id && !empty($request->dividion_id) ) {
            $order_ids = Order::where('product_cat_id', $request->dividion_id)->pluck('id');
            // dd($order_ids);
            $query->whereIn('order_id', $order_ids);
            // $query->where('orders.product_cat_id',$this->dividion_id);
        }


        if (request()->get('pending_status') != '' && request()->get('pending_status') != NULL) {
            $status = match ((string) request()->get('pending_status')) {
                '0' => 'pending',
                '2' => 'partial',
                '1' => 'dispatched',
                default => '',
            };
            $query->whereHas('orders', fn ($orderQuery) => $orderQuery->dispatchStatus($status));
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query->latest();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('sales-table')
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
