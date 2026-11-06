<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(Request $request): RedirectResponse
    // {
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // event(new Registered($user));

        // Auth::login($user);

        // return redirect(RouteServiceProvider::HOME);
    // }

    public function fnStore(Request $request): JsonResponse{
        Log::info('Registration attempt', ['ip' => $request->ip()]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile_no' => ['required', 'regex:/^01[3-9]\d{8}$/', 'unique:users'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users'],
            'recaptchaToken' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'mobile_no.regex' => 'The mobile number must start with 01 and be 11 digits long.',
            'email.email' => 'The email must be a valid email address.',
            'recaptchaToken.required' => 'Please complete the reCAPTCHA.',
            // 'recaptchaToken.captcha' => 'Invalid reCAPTCHA. Please try again.',
        ]);

        DB::beginTransaction();

        try {
            $customer = Customer::where('mobile_no',$validated['mobile_no'])->first();
            // Customer creation
            if(!$customer){
                $customer = Customer::create([
                    'department_id' => 1,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'mobile_no' => $validated['mobile_no'],
                    'address' => '',
                    'status' => 1,
                    'user_id' => Auth::id(),
                ]);
            }
            
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['mobile_no'],
                'mobile_no' => $validated['mobile_no'],
                'email' => $validated['email'],
                'role_id' => 4,
                'department_id' => 1,
                'customer_id' => $customer->id,
                'password' => Hash::make($validated['password']),
            ]);

            event(new Registered($user));

            Auth::login($user);

            if (Auth::check()) {
                $sessionId = Session::get('session_id') ?? Session::getId();
                $cart = Cart::where('session_id', $sessionId)->first();
                if ($cart) {
                    $cart->user_id = $user->id;
                    $cart->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Registration successful!',
                'user' => $user,
            ], 201);

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Registration failed', ['error' => $exception->getMessage()]);
            return response()->json([
                'errors' => 'Registration failed. Please try again.',
            ], 500);
        }
    }

}
