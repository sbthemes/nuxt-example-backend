<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/users/create', [UserController::class, 'store']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/auth/user', [UserController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::post('/email/verify/resend', [AuthController::class, 'resendEmailVerificationLink']);
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('email.verify');
});
