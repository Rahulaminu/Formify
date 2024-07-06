<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json([
        "message" => "Forbidden access"
    ], 403);
})->name('login');

Route::post('/v1/auth/login', [UserController::class, 'login']);
Route::post('/v1/auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
