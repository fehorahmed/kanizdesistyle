<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller{
    public function index(){
        return view('ecommerce.contact.all');
    }
    public function datatable(){
        $query = Contact::orderBy('id', 'ASC');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function(Contact $contact) {
                $btn = '';
                if(auth()->user()->can('contact_edit')) {
                    // $btn = '<a href="'.route('ecom_contact.edit',['contact'=>$contact->id]).'" class="btn btn-info bg-gradient-info btn-sm btn-edit"><i class="fa fa-edit"></i></a>'.'&nbsp;';
                }
                return $btn;
            })
            ->editColumn('status', function(Contact $contact) {
                if ($contact->status == 1)
                    return '<span title="Inactive" class="badge badge-success">Active</span>';
                else
                    return '<span title="Active" class="badge badge-danger">Inactive</span>';
            })

            ->rawColumns(['action','status','image'])
            ->toJson();
    }
    public function add(){
        $maxSort = (Contact::max('sort') ?? 0) + 1;
        return view('ecommerce.contact.add',compact('maxSort'));
    }
    public function addPost(Request $request){
        $request->validate([
            'type' => 'required',
            'image' => 'required|file',
        ]);

        try {
            $contact = new Contact();
            if ($request->image){
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/contact';
                $file->move($destinationPath, $filename);
                $path = 'uploads/contact/' . $filename;
                $contact->image = $path;
            }
            $contact->type = $request->type;
            $contact->sort = $request->sort;
            $contact->status = $request->status;
            $contact->save();

            return redirect()->route('ecom_contact')->with('message','added successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function contactStore(Request $request){
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'mobile_no' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'message' => 'required|string|regex:/^(?!.*<script.*>)(?!.*<\/script>).*$/',
            // 'recaptchaToken' => 'required|captcha',
            'recaptchaToken' => 'required',
        ], [
            'mobile_no.regex' => 'The mobile number must start with 01 and be 11 digits long.',
            'email.email' => 'The email must be a valid email address.',
            'recaptchaToken.required' => 'Please complete the reCAPTCHA.',
            // 'recaptchaToken.captcha' => 'Invalid reCAPTCHA. Please try again.',
        ]);

        try {
            $contact = new Contact();
            $contact->name = e($validated['name']); // sanitize input
            $contact->email = e($validated['email']); // sanitize input
            $contact->subject = e($validated['subject']); // sanitize input
            $contact->mobile_no = e($validated['mobile_no']); // sanitize input
            $contact->message = strip_tags($validated['message']); // sanitize input, remove any HTML
            $contact->status = 1;
            $contact->save();

            return response()->json([
                'message' => 'Message Sent successful!',
                'contact' => $contact,
            ], 201);
        }catch (\Exception $exception){
            return response()->json([
                'errors' => $exception->getMessage(),
            ], 500);
        }
    }
    public function edit(Contact $contact){
        return view('ecommerce.contact.edit',compact('contact'));
    }
    public function editPost(Contact $contact,Request $request){
        $request->validate([
            'type' => 'required',
            'image' => 'nullable|file',
        ]);

        try {
            if ($request->image){
                if (file_exists($contact->image)){
                    unlink($contact->image);
                }
                $file = $request->file('image');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/contact';
                $file->move($destinationPath, $filename);
                $path = 'uploads/contact/' . $filename;
                $contact->image = $path;
            }
            $contact->type = $request->type;
            $contact->sort = $request->sort;
            $contact->status = $request->status;
            $contact->save();

            return redirect()->route('ecom_contact')->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
    public function getHomeSliders(){
        try{
            $sliders = Contact::where('type',1)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $md_sliders = Contact::where('type',2)->where('status',1)->orderBy('sort','asc')->get()->toArray();
            $group_sliders = Contact::where('type',3)->where('status',1)->orderBy('sort','asc')->get()->toArray();
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
