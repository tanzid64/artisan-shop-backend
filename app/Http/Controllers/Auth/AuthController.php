<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Log Authenticaiton attempts
    private function logAuthentication($userId, $username, $success, $action = 'unknown', $error = null)
    {
        try {
            // Auth Log Entry
            DB::table('auth_logs')->insert([]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // Ensure authentication log table exist
    private function ensureAuthLogTableExists($tableName)
    {
        try {
            // Check if table exist
            $tableExists = DB::select(
                "SELECT COUNT(*) AS count
                FROM information_schema.tables
                WHERE table_schema = 'artisan_shop'
                AND table_name = ?
                ",
                [$tableName]
            );

            if ($tableExists[0]->count == 0) {
                // Create table
                DB::statement(
                    "CREATE TABLE artisan_shop.{$tableName} (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    username VARCHAR(100) NOT NULL,
                    success BOOLEAN DEFAULT FALSE,
                    action VARCHAR(50) DEFAULT 'unknown',
                    error_message TEXT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                    INDEX idx_username_created (username, created_at),
                    INDEX idx_success_created (success, created_at),
                    INDEX idx_action (action),
                    INDEX idx_user_id (user_id),
                    INDEX idx_success_action (success, action)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
                    "
                );

                Log::info("Created authentication log table: {$tableName}");
            }
        } catch (\Exception $e) {
            Log::error('Failed to create auth log table', [
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);
        }
    }

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

    public function logout()
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

    public function refresh()
    {
        try {
            // Get Current Token
            $token = JWTAuth::getToken();

            if (!$token) {
                return $this->responseError("Token not found!", null, 401);
            }

            // Refresh the token
            $token = JWTAuth::refresh($token);

            return $this->responseSuccess(['token' => $token], "Token refreshed successfully!");
        } catch (JWTException $e) {
            return $this->responseError("Token could not be refreshed!", ["server" => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return $this->responseError("Token refresh failed!", ["server" => $e->getMessage()], 500);
        }
    }

    public function profile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return $this->responseSuccess($user, "Profile retrieved successfully!");
        } catch (JWTException $e) {
            return $this->responseError("Invalid Token!", ["server" => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return $this->responseError("Profile retrieval failed!", ["server" => $e->getMessage()], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email is required!',
            'email.email' => 'Please provide a valid email address!',
            'email.exists' => 'Email does not exist!',
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator->errors()->toArray(), "Validation failed!");
        }

        try {
            // Check for existing reset token
            $oldToken = DB::table('password_reset_tokens')->where('email', $request->email)->first();
            if ($oldToken) {
                $token = $oldToken->token;
                // Update token validation time
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->update(['created_at' => now()]);
            } else {
                $token = Str::random(40);
                DB::table('password_reset_tokens')->insert([
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => now(),
                ]);
            }

            // Send Password Reset email

            return $this->responseSuccess([
                'token' => $token,
                'email' => $request->email,
            ], "Password reset email sent successfully. Please check your email.");
        } catch (\Throwable $th) {
            return $this->responseError("Password reset email failed!", ["server" => $th->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request, $token, $email)
    {
        if (empty($token)) {
            return $this->responseError("Token is required!", null, 400);
        }

        if (empty($email)) {
            return $this->responseError("Email is required!", null, 400);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password is required!',
            'password.min' => 'Password must be at least 8 characters long!',
            'password.confirmed' => 'Password confirmation does not match!',
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator->errors()->toArray(), "Validation failed!");
        }

        try {
            // Check if token exists and is valid
            $resetToken = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', $token)
                ->first();

            if (!$resetToken) {
                return $this->responseError("Invalid or expired reset token!", null, 400);
            }

            // Check token expiration (60 minutes)
            $createdAt = Carbon::parse($resetToken->created_at)->timezone('Asia/Dhaka');
            $expiresAt = $createdAt->addMinutes(60);
            $currentTime = Carbon::now('Asia/Dhaka');

            if ($currentTime->greaterThan($expiresAt)) {
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                return $this->responseError("Reset token has expired!", null, 400);
            }

            // Update user password
            $user = User::where('email', $email)->first();
            DB::beginTransaction();
            $updated = $user->update([
                'password' => Hash::make($request->password),
                'updated_at' => now(),
            ]);

            if ($updated) {
                // Delete the reset token
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                DB::commit();
                return $this->responseSuccess(null, "Password reset successful!");
            }

            DB::rollBack();
            return $this->responseError("Password reset failed!", null, 500);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseError("Password reset failed!", ["server" => $th->getMessage()], 500);
        }
    }
}
