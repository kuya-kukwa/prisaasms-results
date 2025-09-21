<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Check if user has the required permission using Spatie Permission
        try {
            if (!$user->hasPermissionTo($permission)) {
                return response()->json([
                    'message' => 'You do not have permission to perform this action.',
                    'required_permission' => $permission
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Permission system error.',
                'error' => $e->getMessage()
            ], 500);
        }

        return $next($request);
    }
}
