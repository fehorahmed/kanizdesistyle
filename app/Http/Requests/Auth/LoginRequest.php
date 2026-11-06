<?php

namespace App\Http\Requests\Auth;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        
        if($this->from && $this->from == 'frontend'){
            $user = User::where('email', $this->email)
                ->orWhere('username', $this->email)
                ->orWhere('mobile_no', $this->email)
                ->first();
        }else{
            $user = User::where('email', $this->email)
                ->orWhere('username', $this->email)
                ->orWhere('mobile_no', $this->email)
                ->where('role_id','!=',4)
                ->first();
        }
        // dd(Auth::login($user, $this->boolean('remember')));
        if (!$user){
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        if ($user->status == 0){
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'status' => trans('auth.deactivated'),
            ]);
        }

        if (!Hash::check($this->password, $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => trans('auth.password'),
            ]);
        }
        if($this->from && $this->from == 'frontend'){
            $sessinonId = Session::get('session_id') ?? Session::getId();
            $cart = Cart::where('session_id', $sessinonId)->first();
            // dd($cart);
            if($cart){
                $cart->user_id = $user->id;
                $cart->save();
            }
        }
        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());

//        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
//            RateLimiter::hit($this->throttleKey());
//
//            throw ValidationException::withMessages([
//                'email' => trans('auth.failed'),
//            ]);
//        }

    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
