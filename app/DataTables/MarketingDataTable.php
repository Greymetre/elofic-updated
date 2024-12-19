<?php

namespace App\DataTables;

use App\Models\Marketing;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class MarketingDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('action', function ($data) {
                $btn = '';
                
                if (auth()->user()->can(['marketing_master_show'])) {
                    $btn .= '<a href="' . route('dealer-appointment.show', $data->id) . '" class="btn btn-info btn-just-icon btn-sm" title="Show Appointment Form" ><i class="material-icons">visibility</i></a>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">' . $btn . '</div>';
            })
            ->editColumn('event_date', function ($data) {
                return isset($data->event_date) ? showdatetimeformat($data->event_date) : '';
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Coupon $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Marketing $model)
    {
        return $model->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('coupons-table')
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
