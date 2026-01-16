<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Allowed roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated via API token
        $token = $request->cookie('auth_token') ?? $request->bearerToken() ?? session('auth_token');
        
        if (!$token) {
            // Try to get from localStorage via JavaScript redirect
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập trang này.');
        }

        // Verify token and get user
        $user = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        $user = $user->tokenable;

        if (!$user) {
            return redirect()->route('login')->with('error', 'Không tìm thấy người dùng.');
        }

        // Check if user has one of the allowed roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Share user data with views
        view()->share('authUser', $user);
        $request->merge(['auth_user' => $user]);

        return $next($request);
    }
}
