<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GuestAuthController as AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/confirm-password', [AuthController::class, 'confirmPassword']);
Route::get('/reset', [AuthController::class, 'reset'])->name('password.reset');

Route::middleware(['auth:guard_guest', 'verify.jwt'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return [$payload = auth()->payload(), Auth::user()];
    });

});
