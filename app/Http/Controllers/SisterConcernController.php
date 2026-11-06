<?php

namespace App\Http\Controllers;

use App\Models\SisterConcern;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SisterConcernController extends Controller
{
    public function index()
    {
        return view('administrator.sister_concern.all');
    }

    public function dataTable()
    {
        $query = SisterConcern::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(SisterConcern $sister_concern) {
                $btn ='<a href="'.route('sister_concern.edit', ['sister_concern' => $sister_concern->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function add()
    {
        return view('administrator.sister_concern.add');
    }

    public function addPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $sister_concern = new SisterConcern();
        $sister_concern->name = $request->name;
        $sister_concern->email = $request->email;
        $sister_concern->mobile_no = $request->mobile_no;
        $sister_concern->address = $request->address;
        $sister_concern->status = $request->status;
        $sister_concern->user_id = Auth::id();
        $sister_concern->save();

        return redirect()->route('sister_concern')->with('message', 'SisterConcern create successfully.');
    }

    public function edit(SisterConcern $sister_concern)
    {
        return view('administrator.sister_concern.edit', compact('sister_concern'));
    }

    public function editPost(SisterConcern $sister_concern, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required'
        ]);

        $sister_concern->name = $request->name;
        $sister_concern->email = $request->email;
        $sister_concern->mobile_no = $request->mobile_no;
        $sister_concern->address = $request->address;
        $sister_concern->status = $request->status;
        $sister_concern->save();

        return redirect()->route('sister_concern')->with('message', 'SisterConcern edit successfully.');
    }
}
