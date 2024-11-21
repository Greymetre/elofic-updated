<?php

namespace App\Http\Controllers;

use App\DataTables\ResignationDataTable;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Resignation;
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
        if($request->ip() != '111.118.252.250'){
            return view('work_in_progress');
        }
        abort_if(Gate::denies('resignation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $branches = Branch::where('active', 'Y')->get();
        $divisions = Division::where('active', 'Y')->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', 29);
        })->where('active', 'Y')->get();
        return $dataTable->render('resignation.index', compact('branches', 'divisions','users'));
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
}
