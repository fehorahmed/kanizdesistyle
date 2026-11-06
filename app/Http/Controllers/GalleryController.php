<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Gallery;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class GalleryController extends Controller{
    public function index(){
        return view('ecommerce.gallery.all');
    }
    public function getGalleries(){
        try{
            $galleries = Gallery::select('title','type','image','video_url','status')->where('status',1)->orderBy('sort','asc')->get()->toArray();
            return response()->json(['success'=>true,'galleries'=>$galleries],200);
        }catch(Exception $exception){
            return response()->json(['success'=>false,'galleries'=>[],'errors'=>$exception->getMessage()],500);
        }
    }
    public function getDoctors(){
        try{
            $doctors = Doctor::select('id','name','speciality','image','status')->where('status',1)->orderBy('id','asc')->get()->toArray();
            return response()->json(['success'=>true,'doctors'=>$doctors],200);
        }catch(Exception $exception){
            return response()->json(['success'=>false,'doctors'=>[],'errors'=>$exception->getMessage()],500);
        }
    }
    public function datatable(){
        $query = Gallery::orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Gallery $gallery) {
                if(auth()->user()->can('gallery_edit')) {
                    $btn = '<a href="'.route('ecom_gallery.edit',['gallery'=>$gallery->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->addColumn('designation',function (Gallery $gallery){
                return $gallery->designation??'';
            })
            ->addColumn('image',function (Gallery $gallery){
                if($gallery->type == 1){
                    return '<a href="'. asset($gallery->image) .'" download><img width="100" src="'. asset($gallery->image) .'" /></a>';
                }
                return '';
            })
            ->addColumn('video_url',function (Gallery $gallery){
                if ($gallery->type == 2 || $gallery->type == 3) {
                    $url = $gallery->video_url;
                    if (preg_match('/(?:youtube\.com\/.*v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId = $matches[1];
                        $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                    } else {
                        $embedUrl = $url;
                    }

                    return '<iframe width="150" height="100" src="' . $embedUrl . '" title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
                }

                return '';
            })
            ->editColumn('status', function(Gallery $gallery) {
                if ($gallery->status == 1)
                    return '<span title="Active" class="badge badge-success">Active</span>';
                else
                    return '<span title="Inactive" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','video_url','image'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Gallery::max('sort') ?? 0) + 1;
        return view('ecommerce.gallery.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $rules = [
            'title' => 'required|string',
            'type' => 'required|string',
        ];
        if($request->type == 1){
            $rules['image'] = 'required|file';
        }elseif($request->type == 2){
            $rules['video_link'] = 'required|string';
        }
        $request->validate($rules);
        try {
            $gallery = new Gallery();
            $gallery->title = $request->title;
            if ($request->image){
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/gallery';
                $file->move($destinationPath, $filename);
                $path = 'uploads/gallery/' . $filename;
                $gallery->image = $path;
            }
            $gallery->video_url = $request->video_link;
            $gallery->type = $request->type;
            $gallery->sort = $request->sort;
            $gallery->status = $request->status;
            $gallery->save();

            return redirect()->route('ecom_gallery')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function edit(Gallery $gallery){
        return view('ecommerce.gallery.edit',compact('gallery'));
    }
    public function editPost(Gallery $gallery,Request $request){
        $rules = [
            'title' => 'required|string',
            'type' => 'required|string',
        ];
        if($request->type == 1){
            $rules['image'] = 'required|file';
        }elseif($request->type == 2){
            $rules['video_link'] = 'required|string';
        }
        $request->validate($rules);
        try {
            $gallery->title = $request->title;
            $gallery->video_url = $request->video_link;
            if ($request->image){
                if (file_exists($gallery->image)){
                    unlink($gallery->image);
                }
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/gallery';
                $file->move($destinationPath, $filename);
                $path = 'uploads/gallery/' . $filename;
                $gallery->image = $path;
            }
            $gallery->type = $request->type;
            $gallery->status = $request->status;
            $gallery->save();

            return redirect()->route('ecom_gallery')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
}
