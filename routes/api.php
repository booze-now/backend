<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';

Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout'])->middleware('auth:api');
// Route::group(['middleware'=>['auth:api']],function(){ Route::post('/logout', 'Api\AuthController@logout'); });


// Route::get('drink/scheme', [\App\Http\Controllers\DrinkController::class, 'scheme']);
Route::apiResource('drinks', \App\Http\Controllers\DrinkController::class);
Route::get('categories/{category}/drinks', [\App\Http\Controllers\DrinkCategoryController::class, 'drinks']);
Route::apiResource('categories', \App\Http\Controllers\DrinkCategoryController::class);
Route::apiResource('drink-units', \App\Http\Controllers\DrinkUnitController::class);

Route::fallback(function () {
    return response()->json([
        'status'    => false,
        'message'   => __('Page not found.'),
    ], 404);
});

/*

Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

// Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
//                 ->middleware('guest')
//                 ->name('login');

Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                ->middleware('guest')
                ->name('password.email');

Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
                ->middleware('guest')
                ->name('password.store');

Route::get('/verify-email/{id}/{hash}', App\Http\Controllers\Auth\VerifyEmailController::class)
                ->middleware(['auth', 'signed', 'throttle:6,1'])
                ->name('verification.verify');

Route::post('/email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
                ->middleware(['auth', 'throttle:6,1'])
                ->name('verification.send');

// Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
//                 ->middleware('auth')
//                 ->name('logout');
*/