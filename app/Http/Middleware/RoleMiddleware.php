<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // TODO: Future will be JWT parse user
        $user = $request->user();
        // Authentication check
        if (!$user) {
            return response()->json([
                'status' => 401,
                'success' => false,
                'message' => 'Authentication required',
                'error' => 'Unauthorized',
                'data' => null
            ], 401);
        }
        // Role check
        if ($user->role !== $role) {
            return response()->json([
                'status' => 403,
                'success' => false,
                'message' => 'Access denied. Insufficient permission',
                'error' => 'Forbidden',
                'data' => [
                    'required_role' => $role,
                    'current_role' => $user->role,
                ]
            ], 403);
        }
        return $next($request);
    }
}
