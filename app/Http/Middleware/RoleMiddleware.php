<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Pastikan user terautentikasi
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 401);
        }

        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek apakah user memiliki role_id yang sesuai
        if ($user->role_id != $role) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        return $next($request);
    }
}

