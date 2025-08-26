<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    }

    public function logout(Request $request)
    {
        //
    }

    public function refresh(Request $request)
    {
        //
    }
}
