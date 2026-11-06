<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    public function index() {
        $departments = Department::where('status',1)->whereIn('id',[3,4,5,6])->get();
        return view('service.all',compact('departments'));
    }

    public function dataTable(Request $request){
        $query = Service::with('department');
        if($request->department != ''){
            $query->where('department_id',$request->department);
        }
        return Datatables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Service $service) {
                // $btn ='<a href="'.route('service.edit', ['service' => $service->id]).'" class="btn btn-primary bg-gradient-primary btn-sm btn-edit"><i class="fa fa-edit"></i></a>';
                // return $btn;
            })
            ->addColumn('department', function(Service $service) {
                return $service->department->name ?? '';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create() {
        $departments = Department::where('status',1)->whereIn('id',[3,4,5,6])->get();
        return view('service.add',compact('departments'));
    }

    public function addPost(Request $request) {
        // dd('hi');
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required',
            'description' => 'nullable|string|max:255',
            'service_cost_price' => 'required',
            'service_sale_price' => 'required',
            'status' => 'required'
        ]);


        $lastService = Service::where('department_id',$request->department)->latest('id')->first();

        // Generate the next code
        $nextCodeNumber = !empty($lastService) ? intval(substr($lastService->code, 3)) + 1 : 1;

        // Format the new code as 'p-000001'
        if($request->department == 3){
            $newCode = 'HS-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }elseif($request->department == 4){
            $newCode = 'AS-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }elseif($request->department == 5){
            $newCode = 'PS-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }elseif($request->department == 6){
            $newCode = 'OS-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }else{
            $newCode = 'AS-' . str_pad($nextCodeNumber, 6, '0', STR_PAD_LEFT);
        }

        $service = new Service();
        $service->name = $request->name;
        $service->department_id = $request->department;
        $service->code = $newCode;
        $service->service_cost_price = $request->service_cost_price;
        $service->service_sale_price = $request->service_sale_price;
        $service->description = $request->description;
        $service->status = $request->status;
        $service->user_id = Auth::id();
        $service->save();

        return redirect()->route('service.index')->with('message', 'Service add successfully.');
    }

    public function edit(Service $service) {
        $departments = Department::where('status',1)->whereIn('id',[3,4,5,6])->get();
        return view('service.edit', compact('service','departments'));
    }

    public function editPost(Service $service, Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required',
            'description' => 'nullable|string|max:255',
            'service_cost_price' => 'required',
            'service_sale_price' => 'required',
            'status' => 'required'
        ]);

        $service->name = $request->name;
        $service->department_id = $request->department;
        $service->service_cost_price = $request->service_cost_price;
        $service->service_sale_price = $request->service_sale_price;
        $service->description = $request->description;
        $service->status = $request->status;
        $service->user_id = Auth::id();;
        $service->save();

        return redirect()->route('service.index')->with('message', 'Service edit successfully.');
    }
}
