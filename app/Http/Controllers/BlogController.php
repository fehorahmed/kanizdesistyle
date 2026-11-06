<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Notice;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller{
    public function index(){
        return view('ecommerce.blog.all');
    }
    public function getBlogs(){
        try{
            $blogs = Blog::with(['user:id,name'])->select('id','user_id','title','image','created_at')->where('status',1)->get()->toArray();
            return response()->json(['status'=>true,'blogs'=>$blogs],200);
        }catch(Exception $exception){
            return response()->json(['status'=>false,'blogs'=>[]],500);
        }
    }
    public function getNotices(){
        try{
            $notices = Notice::with('user')->where('status',1)->get()->toArray();
            return response()->json(['status'=>true,'notices'=>$notices],200);
        }catch(Exception $exception){
            return response()->json(['status'=>false,'notices'=>[]],500);
        }
    }
    public function getBlogDetails($blogId){
        try{
            $blog = Blog::with(['user:id,name,profile_photo'])->select('user_id','title','content','image','created_at')->where('id',$blogId)->first()->toArray();
            $prev_blog = Blog::select('id','title')->where('id','!=',$blogId)->where('id','<',$blogId)->orderBy('id','desc')->first() ?? [];
            $next_blog = Blog::select('id','title')->where('id','!=',$blogId)->where('id','>',$blogId)->orderBy('id','asc')->first() ?? [];
            $latest_blogs = Blog::select('id','title','image','created_at')->where('id','!=',$blogId)->orderBy('id','asc')->get()->toArray() ?? [];

            $blog['content'] = base64_encode(gzdeflate($blog['content'], 5));
            return response()->json(['status'=>true,'blog'=>$blog,'prev_blog'=>$prev_blog,'next_blog'=>$next_blog,'latest_blogs'=>$latest_blogs],200);
        }catch(Exception $exception){
            return response()->json(['status'=>false,'blog'=>[]],500);
        }
    }
    public function datatable(){
        $query = Blog::select('id','title','image','status')->orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Blog $blog) {
                if(auth()->user()->can('blog_edit')) {
                    $btn = '<a href="'.route('ecom_blog.edit',['blog'=>$blog->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->addColumn('image',function (Blog $blog){
                return '<a href="'. asset($blog->image) .'" download><img src="'.asset($blog->image).'" width="100"></a>';
            })
            ->editColumn('status', function(Blog $blog) {
                if ($blog->status == 1)
                    return '<span title="Inactive" class="badge badge-success">Active</span>';
                else
                    return '<span title="Active" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','image'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Blog::max('sort') ?? 0) + 1;
        return view('ecommerce.blog.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $request->validate([
            'title' => 'required',
            'short_content' => 'required',
            'content' => 'required',
            'image' => 'required|file',
        ]);

        try {
            $blog = new Blog();
            if ($request->image){
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/blog';
                $file->move($destinationPath, $filename);
                $path = 'uploads/blog/' . $filename;
                $blog->image = $path;
            }
            $blog->title = $request->title;
            $blog->short_content = $request->short_content;
            $blog->content = $request->content;
            $blog->sort = $request->sort;
            $blog->status = $request->status;
            $blog->user_id = auth()->id();
            $blog->save();

            return redirect()->route('ecom_blog')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function edit(Blog $blog){
        return view('ecommerce.blog.edit',compact('blog'));
    }
    public function editPost(Blog $blog,Request $request){
        $request->validate([
            'title' => 'required',
            'short_content' => 'required',
            'content' => 'required',
            'image' => 'nullable|file',
        ]);

        try {
            if ($request->image){
                if (file_exists($blog->image)){
                    unlink($blog->image);
                }
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/blog';
                $file->move($destinationPath, $filename);
                $path = 'uploads/blog/' . $filename;
                $blog->image = $path;
            }
            $blog->title = $request->title;
            $blog->short_content = $request->short_content;
            $blog->content = $request->content;
            $blog->sort = $request->sort;
            $blog->status = $request->status;
            $blog->save();

            return redirect()->route('ecom_blog')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
}
