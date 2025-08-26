<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 401, 'success' => false, 'message' => 'Token is Invalid'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 401, 'success' => false, 'message' => 'Token is Expired'], 401);
        } catch (JWTException | Exception $e) {
            return response()->json(['status' => 404, 'success' => false, 'message' => 'Authorization Token not found'], 404);
        }
        return $next($request);
    }
}
