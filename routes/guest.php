<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GuestAuthController as AuthController;
use App\Http\Controllers\GuestController as GuestController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/confirm-password', [AuthController::class, 'confirmPassword']);
Route::get('/reset', [AuthController::class, 'reset']);
Route::get('/drinks', [\App\Http\Controllers\DrinkController::class, 'index']);
Route::get('/menu', [\App\Http\Controllers\DrinkController::class, 'menu']);
Route::get('/menu-tree', [\App\Http\Controllers\DrinkController::class, 'menuTree']);

Route::middleware(['auth:guard_guest', 'verify.jwt'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [GuestController::class, 'me']);
    Route::post('/update-self', [GuestController::class, 'updateSelf']);

});
