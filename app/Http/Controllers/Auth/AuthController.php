<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Validate the request
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator->errors()->toArray(), "Validation failed!");
        }

        try {
            // Attempt to login
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return $this->responseError("Invalid Credentials!", null);
            }
            // Get Authenticated user
            $user = JWTAuth::user();
            $data = [
                'token' => $token,
                'token_type' => "Bearer",
                'access_token_expires_in' =>  config('jwt.ttl') * 60,
                'refresh_token_expires_in' => config('jwt.refresh_ttl') * 60,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                ],
            ];
            // Return the token
            return $this->responseSuccess($data, "Login successful!");
        } catch (JWTException $e) {
            return $this->responseError("Could not create token!", ["server" => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return $this->responseError("Login Failed!", ["server" => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Invalidate the JWT token
            JWTAuth::invalidate(JWTAuth::parseToken());

            return $this->responseSuccess(null, "Logout successful!");
        } catch (JWTException $e) {
            return $this->responseError("Could not create token!", ["server" => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return $this->responseError("Login Failed!", ["server" => $e->getMessage()], 500);
        }
    }

    public function refresh(Request $request)
    {
        //
    }
}
