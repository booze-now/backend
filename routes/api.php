<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
