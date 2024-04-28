<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GuestAuthController as AuthController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\GuestController as GuestController;
use App\Http\Controllers\OrderController;

Route::post('/register', [AuthController::class, 'register']); // +regisztráció
Route::post('/confirm-registration', [AuthController::class, 'confirmRegistration']); // +regisztráció
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // +elfelejtett jelszó emlékeztető levél küldés
Route::post('/reset-password', [AuthController::class, 'resetPassword']); // +elfelejtett jelszó, változtatás change-forgotten-password
Route::get('/reset', [AuthController::class, 'reset']); // jelszó reset link request
Route::post('/verify/resend', [AuthController::class, 'resendEmailVerificationMail']) // email megerősítés újraküldése
    ->middleware(['throttle:6,1']);

Route::get('/refresh', [AuthController::class, 'refresh'])->middleware(['refresh.jwt']); // token frissítés

Route::get('/menu', [DrinkController::class, 'menu']);
Route::get('/menu-tree', [DrinkController::class, 'menuTree']);

Route::get('/drinks', [DrinkController::class, 'index']);
Route::get('/drinks/{drink}', [DrinkController::class, 'show']);

//ordering
Route::post('/{userId}/cart', [OrderController::class, 'placeOrder']);


Route::middleware(['auth:guard_guest'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [GuestController::class, 'me']);
    Route::post('/update-self', [GuestController::class, 'updateSelf']);
});
