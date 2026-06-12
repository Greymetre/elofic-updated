<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PromotionalActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\PromotionalActivityExport;
use App\Exports\ActivityAttendeesExport;
use Maatwebsite\Excel\Facades\Excel;


class PromotionalActivitysController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = PromotionalActivity::with([
                'retailer:id,shop_name,owner_name',
                'distributor:id,trade_name,legal_name',
                'approvedBy:id,name',
                'createdBy:id,name'
            ])
            ->latest();
            return DataTables::of($query)
                ->addIndexColumn()

                ->editColumn('activity_date', function ($row) {
                    return $row->activity_date
                        ? date('d-m-Y', strtotime($row->activity_date))
                        : '-';
                })

                ->addColumn('retailer_name', function ($row) {
                    return optional($row->retailer)->company_name ?? '-';
                })

                ->addColumn('distributor_name', function ($row) {
                    return optional($row->distributor)->name ?? '-';
                })

                ->addColumn('approved_by_name', function ($row) {
                    return optional($row->approvedBy)->name ?? '-';
                })

                ->addColumn('created_by_name', function ($row) {
                    return optional($row->createdBy)->name ?? '-';
                })

                ->addColumn('action', function ($row) {

                    $buttons = '';

                    $buttons .= '<a href="' . route('promotional_activity.show', $row->id) . '" 
                                    class="btn btn-info btn-sm">
                                    <i class="material-icons">visibility</i>
                                </a>';

                    $buttons .= ' <a href="' . route('promotional_activity.edit', $row->id) . '" 
                                    class="btn btn-primary btn-sm">
                                    <i class="material-icons">edit</i>
                                </a>';

                    $buttons .= ' <button type="button"
                                    value="' . $row->id . '"
                                    class="btn btn-danger btn-sm delete">
                                    <i class="material-icons">delete</i>
                                </button>';

                    return $buttons;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('promotional_activities.index');
    }

    public function export()
    {
        return Excel::download(
            new PromotionalActivityExport,
            'promotional_activities.xlsx'
        );
    }
    public function exportAttendees()
    {
        return Excel::download(
            new ActivityAttendeesExport(),
            'activity_attendees.xlsx'
        );
    }
}