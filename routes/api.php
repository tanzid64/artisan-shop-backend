<?php

use App\Http\Controllers\Auth\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'name' => 'Artisan Shop API Collection',
        'Timezone' => "Asia/Dhaka",
        'Date' => now()->format('Y-m-d H:i:s'),
        'Version' => "1.0.0",
        'Author' => "Artisan Shop",
        'Author URL' => "https://artisan-shop.com",
        'Documentation' => "https://artisan-shop.com/docs",
        'Support' => "https://artisan-shop.com/support",
    ];
});

// ** Auth Routes **
Route::post('login', [AuthController::class, 'login'])->name('login');

// ** Protected Routes **
Route::middleware('jwt')->group(function () {
    // Auth Routes
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
