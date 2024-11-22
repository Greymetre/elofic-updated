<?php

namespace App\Http\Controllers;

use App\DataTables\ResignationDataTable;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Resignation;
use App\Models\ResignationCheckList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;

class ResignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ResignationDataTable $dataTable, Request $request)
    {
        abort_if(Gate::denies('resignation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', 'Y')->get();
        return $dataTable->render('resignation.index', compact('branches', 'divisions', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $branches = Branch::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        return view('resignation.form', compact('branches', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['submit_date'] = now()->format('Y-m-d');
        $resignation = Resignation::create($request->all());
        return redirect(route('resignations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function show(Resignation $resignation)
    {
        return view('resignation.show', compact('resignation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function edit(Resignation $resignation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Resignation $resignation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Resignation  $resignation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Resignation $resignation)
    {
        //
    }

    public function download(Request $request)
    {
        dd($request->all());
    }

    public function update_checklist(Request $request)
    {
        $update = ResignationCheckList::updateOrCreate(['resignation_id' => $request->data['resignation_id']], [
            'document_file' => $request->data['document_file'],
            'exit_interview' => $request->data['exit_interview'],
            'advance' => $request->data['advance'],
            'laptop' => $request->data['laptop'],
            'sim_card' => $request->data['sim_card'],
            'keys' => $request->data['keys'],
            'visiting_card' => $request->data['visiting_card'],
            'income_tax' => $request->data['income_tax'],
            'laptop_bag' => $request->data['laptop_bag'],
            'expense_voucher' => $request->data['expense_voucher'],
            'crm_id' => $request->data['crm_id'],
            'unpaid_salary' => $request->data['unpaid_salary'],
            'data_email' => $request->data['data_email'],
            'id_card' => $request->data['id_card'],
            'payable_expense' => $request->data['payable_expense'],
            'pen_drive' => $request->data['pen_drive'],
            'bouns' => $request->data['bouns'],
        ]);

        if($update){
            return response()->json(['status'=>'success', 'message' => 'Checklist updated succussfully !!']);
        }else{
            return response()->json(['status'=>'error', 'message' => 'Somthing went wrong.']);
        }
    }

    public function resignation_status_change(Request $request)
    {
        $update = Resignation::where('id', $request->id)->update(['status' => $request->status, 'remark' => $request->remark]);

        if($update){
            return response()->json(['status'=>'success', 'message' => 'Status updated succussfully !!']);
        }else{
            return response()->json(['status'=>'error', 'message' => 'Somthing went wrong.']);
        }
    }
}
