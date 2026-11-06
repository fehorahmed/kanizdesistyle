<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller{
    public function index(){
        return view('ecommerce.banner.all');
    }
    public function datatable(){
        $query = Banner::orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Banner $banner) {
                if(auth()->user()->can('banner_edit')) {
                    $btn = '<a href="'.route('ecom_banner.edit',['banner'=>$banner->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->addColumn('type',function (Banner $banner){
                if($banner->type == 1){
                    return 'Banner';
                }elseif($banner->type == 2){
                    return 'MD Banner';
                }elseif($banner->type == 3){
                    return 'Group Banner';
                }
            })
            ->editColumn('image', function (Banner $banner) {
                $preview_html = '';
                $preview_html .= '<div class="zoom-gallery">';
                $preview_html .= '<a class="image_preview" title="'. $banner->name .'" data-source="'. asset(file_exists($banner->image) ? $banner->image : 'img/no-image.webp') .'" href="'. asset(file_exists($banner->image) ? $banner->image : 'img/no-image.webp') .'"><img height="50px" src="'.asset(file_exists($banner->image) ? $banner->image : 'img/no-image.webp').'"></a>';
                $preview_html .= '</div>';
                return $preview_html;
            })
            ->editColumn('status', function(Banner $banner) {
                if ($banner->status == 1)
                    return '<span title="Inactive" class="badge badge-success">Active</span>';
                else
                    return '<span title="Active" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','image'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Banner::max('sort') ?? 0) + 1;
        return view('ecommerce.banner.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $request->validate([
            'type' => 'required',
            'image' => 'required|file',
        ]);

        try {
            $banner = new Banner();
            if ($request->image){
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/banner';
                $file->move($destinationPath, $filename);
                $path = 'uploads/banner/' . $filename;
                $banner->image = $path;
            }
            $banner->type = $request->type;
            $banner->sort = $request->sort;
            $banner->status = $request->status;
            $banner->save();

            return redirect()->route('ecom_banner')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function edit(Banner $banner){
        return view('ecommerce.banner.edit',compact('banner'));
    }
    public function editPost(Banner $banner,Request $request){
        $request->validate([
            'type' => 'required',
            'image' => 'nullable|file',
        ]);

        try {
            if ($request->image){
                if (file_exists($banner->image)){
                    unlink($banner->image);
                }
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/banner';
                $file->move($destinationPath, $filename);
                $path = 'uploads/banner/' . $filename;
                $banner->image = $path;
            }
            $banner->type = $request->type;
            $banner->sort = $request->sort;
            $banner->status = $request->status;
            $banner->save();

            return redirect()->route('ecom_banner')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function getHomeSliders(){
        try{
            $sliders = Banner::select('type','sort','image','status')->where('type',1)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $md_sliders = Banner::select('type','sort','image','status')->where('type',2)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $group_sliders = Banner::select('type','sort','image','status')->where('type',3)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $categories = Category::select('id','name','image','status')->where('status',1)->orderBy('name','asc')->get()->toArray();
            
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
            $categories = Category::select('id','name','image','status')->where('status',1)->orderBy('name','asc')->get()->toArray();
            
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
