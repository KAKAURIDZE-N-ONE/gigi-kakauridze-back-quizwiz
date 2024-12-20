<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/log-in', [AuthController::class, 'logIn']);
Route::post('/sign-up', [AuthController::class, 'signUp']);

Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:sanctum', 'verified:sanctum');
Route::get(
    '/email/verify/{id}/{hash}',
    [AuthController::class, 'verifyEmail']
)->middleware(['auth', 'signed'])->name('verification.verify');

Route::view(
    '/email/verify',
    'auth.verify-email'
)->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', [AuthController::class, 'verifyNotfication'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');
