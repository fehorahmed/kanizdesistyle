<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            // Get the authenticated user
            $user = Auth::user();

            // Check the user's status
            if ($user->status === 0) {
                Auth::logout(); // Logout the user
                return redirect()->route('login')->with('error', 'Your account is deactivated.');
            }
        }

        return $next($request);
    }
}
