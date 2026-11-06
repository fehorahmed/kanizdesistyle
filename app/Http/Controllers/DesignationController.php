<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DesignationController extends Controller
{
    public function index() {
        return view('patient_appointment.designation.all');
    }

    public function dataTable()
    {
        $query = Designation::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Designation $designation) {
                $btn ='<a href="'.route('designation.edit', ['designation' => $designation->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function add() {
        return view('patient_appointment.designation.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $designation = new Designation();
        $designation->name = $request->name;
        $designation->status = $request->status;
        $designation->user_id = Auth::id();
        $designation->save();

        return redirect()->route('designation')->with('message', 'Designation add successfully.');
    }

    public function edit(Designation $designation) {
        return view('patient_appointment.designation.edit', compact('designation'));
    }

    public function editPost(Designation $designation, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $designation->name = $request->name;
        $designation->status = $request->status;
        $designation->save();

        return redirect()->route('designation')->with('message', 'Designation edit successfully.');
    }
}