<?php

namespace App\DataTables;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class ProductDataTable extends DataTable
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
            ->addColumn('expiry_info', function ($row) {
                return $row->expiry_interval_preiod . '(<small>' . $row->expiry_interval . '</small>)';
            })
            ->addColumn('action', function ($query) {
                  $btn = '';
                  $activebtn ='';
                  if(auth()->user()->can(['product_edit']))
                  {
                    $btn = $btn.'<a href="'.url("products/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm">
                                    <i class="material-icons">edit</i>
                                </a>';
                  }
                  if(auth()->user()->can(['product_show']))
                  {
                    $btn = $btn.'<a href="'.url("products/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm">
                                    <i class="material-icons">visibility</i>
                                </a>';
                    // $btn = $btn.'<a href"'.url("products/".encrypt($query->id)).'" class="btn btn-theme btn-just-icon btn-sm show" >
                    //       <i class="material-icons">visibility</i>
                    //     </a>';
                  }
                  if(auth()->user()->can(['product_delete']))
                  {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.product.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                  }
                  if(auth()->user()->can(['product_active']))
                  {
                    $active = ($query->active == 'Y') ? 'checked="" value="'.$query->active.'"' : 'value="'.$query->active.'"';
                    $activebtn = '<div class="togglebutton">
                                <label>
                                  <input type="checkbox"'.$active.' id="'.$query->id.'" class="activeRecord">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
                  }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>'.$activebtn;
            })
            ->addColumn('npd', function ($row) {
                $launch = $row->effective_launch_date;
                $eligible = $launch && $launch->gte(Product::npdCutoff());
                $launchText = $launch ? $launch->format('d M Y') : '-';
                if ($row->is_npd) {
                    $badge = '<span class="badge" style="background:#7c3aed;color:#fff;padding:5px 9px;border-radius:12px;font-weight:600;">NPD</span>';
                } elseif ($eligible) {
                    $badge = '<span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;padding:4px 9px;border-radius:12px;font-weight:600;" title="Launched within 1 year, but removed from NPD manually">Existing (manual)</span>';
                } else {
                    $badge = '<span class="badge" style="background:#f1f5f9;color:#475569;padding:5px 9px;border-radius:12px;font-weight:600;">Existing</span>';
                }
                $html = '<div style="display:flex;flex-direction:column;align-items:flex-start;gap:4px;">' . $badge
                    . '<small style="color:#94a3b8;">Launch: ' . e($launchText) . '</small>';
                if ($eligible && auth()->user()->can(['product_edit'])) {
                    $checked = $row->is_npd ? 'checked' : '';
                    $html .= '<div class="togglebutton" title="Tag / untag as NPD"><label style="margin:0;">'
                        . '<input type="checkbox" class="npdToggle" data-id="' . $row->id . '" ' . $checked . '>'
                        . '<span class="toggle"></span><small>NPD</small></label></div>';
                }
                return $html . '</div>';
            })
            ->addColumn('image', function ($query) {
                $product_image = !empty($query->product_image) ? $query->product_image : asset('assets/img/placeholder.jpg') ;
                return '<img src="'.$product_image.'" border="0" width="70" class="img-rounded imageDisplayModel" align="center" />';
            })
            ->rawColumns(['action','image', 'expiry_info', 'npd']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model, Request $request)
    {
        $data = $model->with('createdbyname','categories','subcategories','brands','unitmeasures','productdetails','productpriceinfo');
        if($request->category_id && !empty($request->category_id)){
            $data->where('category_id', $request->category_id);
        }
        if($request->active && !empty($request->active)){
            $data->where('active', $request->active);
        }
        if ($request->npd === 'NPD') {
            $data->npd();
        } elseif ($request->npd === 'EXISTING') {
            $data->existing();
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
