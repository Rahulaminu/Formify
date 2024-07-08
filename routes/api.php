<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/register', [UserController::class, 'register']);
    Route::prefix('auth')->group(function () {
        Route::post('/login', [UserController::class, 'login']);
        Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/forms', [FormController::class, 'index']);
        Route::post('/forms', [FormController::class, 'store']);
        Route::get('/forms/{slug}', [FormController::class, 'show']);

        Route::post('/forms/{form:slug}/questions', [QuestionController::class, 'store']);
        Route::delete('/forms/{form:slug}/questions/{question}', [QuestionController::class, 'destroy']);

        Route::post('/forms/{form}/responses', [ResponseController::class, 'store']);
        Route::get('/forms/{form:slug}/responses', [ResponseController::class, 'index']);
    });

    Route::get('/', function () {
        return response()->json([
            "message" => "Forbidden access"
        ], 403);
    })->name('login');
});
