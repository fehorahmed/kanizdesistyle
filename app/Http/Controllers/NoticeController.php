<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class NoticeController extends Controller{
    public function index(){
        return view('ecommerce.notice.all');
    }
    public function datatable(){
        $query = Notice::orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Notice $notice) {
                if(auth()->user()->can('notice_edit')) {
                    $btn = '<a href="'.route('ecom_notice.edit',['notice'=>$notice->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->addColumn('pdf_file',function (Notice $notice){
                return '<a target="_blank" href="'.asset($notice->pdf_file).'"><img src="'. asset('img/pdf.png') .'"/></a>';
            })
            ->editColumn('status', function(Notice $notice) {
                if ($notice->status == 1)
                    return '<span title="Inactive" class="badge badge-success">Active</span>';
                else
                    return '<span title="Active" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','pdf_file'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Notice::max('sort') ?? 0) + 1;
        return view('ecommerce.notice.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $request->validate([
            'title' => 'required',
            'pdf_file' => 'required|file',
        ]);

        try {
            $notice = new Notice();
            if ($request->pdf_file){
                $file = $request->file('pdf_file');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/notice';
                $file->move($destinationPath, $filename);
                $path = 'uploads/notice/' . $filename;
                $notice->pdf_file = $path;
            }
            $notice->title = $request->title;
            $notice->sort = $request->sort;
            $notice->status = $request->status;
            $notice->user_id = Auth::id();
            $notice->save();

            return redirect()->route('ecom_notice')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function edit(Notice $notice){
        return view('ecommerce.notice.edit',compact('notice'));
    }
    public function editPost(Notice $notice,Request $request){
        $request->validate([
            'title' => 'required',
            'pdf_file' => 'nullable|file',
        ]);

        try {
            if ($request->pdf_file){
                if (file_exists($notice->pdf_file)){
                    unlink($notice->pdf_file);
                }
                $file = $request->file('pdf_file');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/notice';
                $file->move($destinationPath, $filename);
                $path = 'uploads/notice/' . $filename;
                $notice->pdf_file = $path;
            }
            $notice->title = $request->title;
            $notice->sort = $request->sort;
            $notice->status = $request->status;
            $notice->save();

            return redirect()->route('ecom_notice')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function getHomeSliders(){
        try{
            $sliders = Notice::where('type',1)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $md_sliders = Notice::where('type',2)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $group_sliders = Notice::where('type',3)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $categories = Category::where('status',1)->orderBy('name','asc')->get()->toArray();
            
            return response()->json([
                'sliders'=>$sliders,
                'md_sliders'=>$md_sliders,
                'group_sliders'=>$group_sliders,
                'categories'=>$categories,
            ],200);
        }catch(Exception $exception){
            return response()->json([
                'errors'=>$exception->getMessage()
            ],500);
        }
    }
    public function getHomeCategories(){
        try{
            $categories = Category::where('status',1)->orderBy('name','asc')->get()->toArray();
            
            return response()->json([
                'categories'=>$categories,
            ],200);
        }catch(Exception $exception){
            return response()->json([
                'errors'=>$exception->getMessage()
            ],500);
        }
    }
}
