<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // check if user is loggedIn
        if (!Auth::check()) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Un-Authorized access'
            ], 403);
        }
        if (in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'You do not have permission to access this resource'
        ], 403);
    }
}
