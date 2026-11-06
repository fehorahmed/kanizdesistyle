<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    public function index(){
        return view('ecommerce.page.all');
    }
    public function datatable(){
        $query = Page::select('id','title','slug','image','status')->orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Page $page) {
                if(auth()->user()->can('blog_edit')) {
                    $btn = '<a href="'.route('ecom_page.edit',['page'=>$page->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->addColumn('image',function (Page $page){
                return '<a href="'. asset($page->image) .'" download><img src="'.asset($page->image).'" width="100"></a>';
            })
            ->editColumn('status', function(Page $page) {
                if ($page->status == 1)
                    return '<span title="Inactive" class="badge badge-success">Active</span>';
                else
                    return '<span title="Active" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','image'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Page::max('sort') ?? 0) + 1;
        return view('ecommerce.page.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $request->validate([
            'title' =>[
                'required','max:255',
                Rule::unique('pages')
            ],
            'short_content' => 'nullable',
            'content' => 'required',
            'image' => 'nullable|file',
        ]);

        try {
            $page = new Page();
            if ($request->image){
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/page';
                $file->move($destinationPath, $filename);
                $path = 'uploads/page/' . $filename;
                $page->image = $path;
            }
            $page->title = $request->title;
            $page->slug = Str::slug($request->title);
            $page->short_content = $request->short_content;
            $page->content = $request->content;
            $page->sort = $request->sort;
            $page->status = $request->status;
            $page->user_id = auth()->id();
            $page->save();

            return redirect()->route('ecom_page')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function edit(Page $page){
        return view('ecommerce.page.edit',compact('page'));
    }
    public function editPost(Page $page,Request $request){
        $request->validate([
            'title' =>[
                'required','max:255',
                Rule::unique('pages')
                    ->ignore($page)
            ],
            'short_content' => 'nullable',
            'content' => 'required',
            'image' => 'nullable|file',
        ]);

        try {
            if ($request->image){
                if (file_exists($page->image)){
                    unlink($page->image);
                }
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/page';
                $file->move($destinationPath, $filename);
                $path = 'uploads/page/' . $filename;
                $page->image = $path;
            }
            $page->title = $request->title;
            $page->slug = Str::slug($request->title);
            $page->short_content = $request->short_content;
            $page->content = $request->content;
            $page->sort = $request->sort;
            $page->status = $request->status;
            $page->save();

            return redirect()->route('ecom_page')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
}
