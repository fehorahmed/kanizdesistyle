<?php

namespace App\Http\Controllers;

use App\Enumeration\Role;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.profile_edit', [
            'user' => $request->user(),
        ]);
    }

    public function darkModeChange()
    {
        $user = auth()->user();
        $user->theme_mode = $user->theme_mode == 1 ? 0 : 1;
        $user->save();
        return redirect()->back();
    }

    public function editPost(Request $request)
    {

        $request->validate([
            'name' => ['string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'username' => ['required', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'mobile_no' => ['nullable', 'max:255', Rule::unique('users')->ignore(auth()->user()->id)],
            'profile_photo' => ['nullable'],
        ]);
        $user = auth()->user();
        if ($request->file('profile_photo')) {
            if (file_exists($user->profile_photo)){
                unlink($user->profile_photo);
            }
            // Upload Image
            $file = $request->file('profile_photo');
            $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
            $destinationPath = 'uploads/user/profile_photo';
            $file->move($destinationPath, $filename);
            $path = 'uploads/user/profile_photo/' . $filename;

            $user->profile_photo = $path;
        }

        $signaturePhotoPath = $user->signature_photo;
        if ($request->hasFile('signature_photo')){
            if (file_exists($user->signature_photo)){
                unlink($user->signature_photo);
            }
            // Upload Image
            $file = $request->file('signature_photo');
            $filename = Uuid::uuid1()->toString() . '.' . $file->extension();
            $destinationPath = 'uploads/user/signature_photo';
            $file->move($destinationPath, $filename);
            $signaturePhotoPath = 'uploads/user/signature_photo/' . $filename;
        }

        $user->signature_photo = $signaturePhotoPath;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->mobile_no = $request->mobile_no;
        $user->email = $request->email;
        $user->save();
        return redirect()->back()->with('message', 'Profile updated');

    }

    public function passwordEdit(Request $request)
    {

        return view('profile.password_edit', [
            'user' => $request->user(),
        ]);

    }

    public function passwordUpdate(Request $request)
    {

        // Validate the request data
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Incorrect current password');
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('message', 'Password updated');

    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

}
