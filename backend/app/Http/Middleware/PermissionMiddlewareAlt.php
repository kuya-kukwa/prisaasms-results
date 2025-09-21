<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionMiddlewareAlt
{
    /**
     * Handle an incoming request using direct database queries
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $user = Auth::user();

        // Check permission via direct database queries
        $hasPermission = $this->userHasPermission($user->id, $permission);

        if (!$hasPermission) {
            return response()->json([
                'message' => 'You do not have permission to perform this action.',
                'required_permission' => $permission
            ], 403);
        }

        return $next($request);
    }

    /**
     * Check if user has permission via database queries
     */
    private function userHasPermission($userId, $permissionName): bool
    {
        // Check direct user permissions
        $directPermission = DB::table('model_has_permissions')
            ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_permissions.model_id', $userId)
            ->where('model_has_permissions.model_type', 'App\Models\User')
            ->where('permissions.name', $permissionName)
            ->exists();

        if ($directPermission) {
            return true;
        }

        // Check role-based permissions
        $rolePermission = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_roles.model_id', $userId)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->where('permissions.name', $permissionName)
            ->exists();

        return $rolePermission;
    }
}
