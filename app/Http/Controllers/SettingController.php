<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller{
    public function update(Setting $setting){
        return view('ecommerce.setting.update',compact('setting'));
    }
    public function updatePost(Setting $setting,Request $request){
        // dd($request->all());
        $request->validate([
            'banner_image_1' => 'nullable|file',
            'banner_image_2' => 'nullable|file',
            'banner_image_3' => 'nullable|file',
            'banner_image_4' => 'nullable|file',
            'banner_image_5' => 'nullable|file',
        ]);

        try {
            if ($request->banner_image_1){
                if (file_exists($setting->banner_image_1)){
                    unlink($setting->banner_image_1);
                }
                $file = $request->file('banner_image_1');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/setting';
                $file->move($destinationPath, $filename);
                $path = 'uploads/setting/' . $filename;
                $setting->banner_image_1 = $path;
            }
            $setting->banner_image_link_1 = $request->banner_image_link_1;
            if($request->banner_image_status_1){
                $setting->banner_image_status_1 = 1;
            }else{
                $setting->banner_image_status_1 = 0;
            }
            if ($request->banner_image_2){
                if (file_exists($setting->banner_image_2)){
                    unlink($setting->banner_image_2);
                }
                $file = $request->file('banner_image_2');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/setting';
                $file->move($destinationPath, $filename);
                $path = 'uploads/setting/' . $filename;
                $setting->banner_image_2 = $path;
            }
            $setting->banner_image_link_2 = $request->banner_image_link_2;
            if($request->banner_image_status_2){
                $setting->banner_image_status_2 = 1;
            }else{
                $setting->banner_image_status_2 = 0;
            }
            if ($request->banner_image_3){
                if (file_exists($setting->banner_image_3)){
                    unlink($setting->banner_image_3);
                }
                $file = $request->file('banner_image_3');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/setting';
                $file->move($destinationPath, $filename);
                $path = 'uploads/setting/' . $filename;
                $setting->banner_image_3 = $path;
            }
            $setting->banner_image_link_3 = $request->banner_image_link_3;
            if ($request->banner_image_status_3){
                $setting->banner_image_status_3 = 1;
            }else{
                $setting->banner_image_status_3 = 0;
            }
            if ($request->banner_image_4){
                if (file_exists($setting->banner_image_4)){
                    unlink($setting->banner_image_4);
                }
                $file = $request->file('banner_image_4');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/setting';
                $file->move($destinationPath, $filename);
                $path = 'uploads/setting/' . $filename;
                $setting->banner_image_4 = $path;
            }
            $setting->banner_image_link_4 = $request->banner_image_link_4;
            if ($request->banner_image_status_4){
                $setting->banner_image_status_4 = 1;
            }else{
                $setting->banner_image_status_4 = 0;
            }
            if ($request->banner_image_5){
                if (file_exists($setting->banner_image_5)){
                    unlink($setting->banner_image_5);
                }
                $file = $request->file('banner_image_5');
                $filename = Uuid::uuid1()->toString().'.'.$file->getClientOriginalExtension();
                $destinationPath = 'uploads/setting';
                $file->move($destinationPath, $filename);
                $path = 'uploads/setting/' . $filename;
                $setting->banner_image_5 = $path;
            }
            $setting->banner_image_link_5 = $request->banner_image_link_5;
            if ($request->banner_image_status_5){
                $setting->banner_image_status_5 = 1;
            }else{
                $setting->banner_image_status_5 = 0;
            }
            $setting->primary_color = $request->primary_color;
            $setting->status = 1;
            $setting->save();

            return redirect()->back()->with('message','Updated successfully');
        }catch (\Exception $exception){
            return redirect()->back()->withInput()->with('error',$exception->getMessage());
        }
    }
}
