<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GuestAuthController as AuthController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\GuestController as GuestController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // +elfelejtett jelszó emlékeztető levél küldés
Route::post('/confirm-password', [AuthController::class, 'confirmPassword']); // +elfelejtett jelszó, változtatás
Route::get('/reset', [AuthController::class, 'reset']); // jelszó reset link request
Route::post('/verify/resend', [AuthController::class, 'resendEmailVerificationMail'])
    ->middleware(['throttle:6,1']);

Route::get('/refresh', [AuthController::class, 'refresh'])->middleware(['refresh.jwt']); // token frissítés

Route::get('/menu', [DrinkController::class, 'menu']);
Route::get('/menu-tree', [DrinkController::class, 'menuTree']);

Route::get('/drinks', [DrinkController::class, 'index']);
Route::get('/drinks/{drink}', [DrinkController::class, 'show']);

Route::middleware(['auth:guard_guest'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [GuestController::class, 'me']);
    Route::post('/update-self', [GuestController::class, 'updateSelf']);
});
