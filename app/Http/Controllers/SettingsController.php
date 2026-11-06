<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function mySqlDbConnection()
    {
        try {
            DB::connection()->getPdo();
            return redirect()->route('login');
        } catch (\Exception $e) {
            return view('global_settings.my_sql_db_connection');
        }

    }

    public function mySqlDbConnectionUpdate(Request $request)
    {
        $request->validate([
            'db_host'=>'required|max:255',
            'db_port'=>'required|max:255',
            'db_name'=>'required|max:255',
            'db_username'=>'required|max:255',
            'db_password'=>'nullable|max:255',
            'app_url'=>'required',
            'asset_url'=>'required',
        ]);

        $this->setEnvValue('DB_HOST', $request->db_host);
        $this->setEnvValue('DB_PORT', $request->db_port);
        $this->setEnvValue('DB_DATABASE', $request->db_name);
        $this->setEnvValue('DB_USERNAME', $request->db_username);
        $this->setEnvValue('DB_PASSWORD', $request->db_password);
        $this->setEnvValue('APP_URL', $request->app_url);
        $this->setEnvValue('ASSET_URL', $request->asset_url);
        // Clear the configuration cache
        Artisan::call('config:clear');
        try {
            DB::connection()->getPdo();
            return redirect()->route('login')
                ->with('success', 'Database configuration updated successfully.');
        } catch (\Exception $e) {
            Artisan::call('config:clear');
            DB::rollBack();
            return redirect()->route('my_sql_db_connection')
                ->withInput()
                ->with('error',$e->getMessage());
        }

    }

    public function globalSettings(Request $request)
    {
        $logoPath = 'img/logo.jpg';
        return view('global_settings.global_settings',compact('logoPath'));

    }
    public function globalSettingsUpdate(Request $request)
    {
        $request->validate([
            'logo'=>'nullable|mimes:png',
            'app_name'=>'required|max:200',
            'app_address'=>'required|max:300',
            'app_contact'=>'required|max:200',
            'app_email'=>'required|max:200',
            'app_url'=>'required',
            'asset_url'=>'required',
            'app_debug'=>'required',
        ]);
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $this->setEnvValue('APP_NAME',$request->app_name);
            $this->setEnvValue('APP_EMAIL',$request->app_email);
            $this->setEnvValue('APP_ADDRESS', $request->app_address);
            $this->setEnvValue('APP_CONTACT', $request->app_contact);
            $this->setEnvValue('APP_URL', $request->app_url);
            $this->setEnvValue('ASSET_URL', $request->asset_url);
            $this->setEnvValue('APP_DEBUG', ($request->app_debug == 'false' ? 'false' : 'true'));

            if ($request->hasFile('logo')){
                // Logo Upload Image
                $file = $request->file('logo');
                $filename = 'logo.jpg';
                $destinationPath = 'img';
                $file->move(public_path($destinationPath), $filename);
            }

            // Clear the configuration cache
            Artisan::call('config:clear');

            DB::commit();
            //Again Login
            Auth::login($user,true);
            return redirect()->back()->with('message','Updated successfully');
        }catch (\Exception $exception){
            DB::rollBack();
            return \redirect()->back()
                ->withInput()
                ->with('error',$exception->getMessage());
        }

    }
    protected function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // If the value contains spaces, wrap it in double quotes
            $value = preg_match('/\s/', $value) ? '"' . $value . '"' : $value;

            $envFileContent = file_get_contents($path);

            // Use regex to replace the existing value or append if the key does not exist
            if (preg_match('/^' . preg_quote($key) . '=/m', $envFileContent)) {
                $envFileContent = preg_replace(
                    '/^' . preg_quote($key) . '=.*/m',
                    $key . '=' . $value,
                    $envFileContent
                );
            } else {
                $envFileContent .= PHP_EOL . $key . '=' . $value;
            }

            file_put_contents($path, $envFileContent);
        }
    }
}
