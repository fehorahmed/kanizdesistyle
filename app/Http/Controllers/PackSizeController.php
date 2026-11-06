<?php

namespace App\Http\Controllers;

use App\Models\PackSize;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PackSizeController extends Controller
{
    public function index() {
        return view('pack_size.pack_size.all');
    }

    public function dataTable()
    {
        $query = PackSize::query();
        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(PackSize $packSize) {
                $btn ='<a href="'.route('pack_size.edit', ['packSize' => $packSize->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function add() {
        return view('pack_size.pack_size.add');
    }

    public function addPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|numeric|max_digits:4',
            'status' => 'required'
        ]);

        $packSize = new PackSize();
        $packSize->name = $request->name;
        $packSize->number = $request->number;
        $packSize->status = $request->status;
        $packSize->user_id = Auth::id();
        $packSize->save();

        return redirect()->route('pack_size')->with('message', 'Box Setting add successfully.');
    }
    public function addInstantyPost(Request $request) {
        $rules = [
            'name' => 'required|string|max:255',
            'number' => 'required|numeric|max_digits:4',
            'status' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $checkPackSize = PackSize::where('name',$request->name)->where('number',$request->number)->first();
        if ($checkPackSize){
            return response()->json(['success' => false, 'message' => 'PackSize already exists!']);
        }
        try{
            $packSize = new PackSize();
            $packSize->name = $request->name;
            $packSize->number = $request->number;
            $packSize->status = $request->status;
            $packSize->user_id = Auth::id();
            $packSize->save();

            return response()->json(['success'=>true,'message'=>'','packSize'=>$packSize]);
        }catch(Exception $exception){
            return response()->json(['success'=>false,'message'=>$exception->getMessage()]);
        }
    }

    public function edit(PackSize $packSize) {
        return view('pack_size.pack_size.edit', compact('packSize'));
    }

    public function editPost(PackSize $packSize, Request $request) {
        // dd('hi');
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|numeric|max_digits:4',
            'status' => 'required'
        ]);

        $packSize->name = $request->name;
        $packSize->number = $request->number;
        $packSize->status = $request->status;
        $packSize->save();

        return redirect()->route('pack_size')->with('message', 'Box Setting edit successfully.');
    }
}
