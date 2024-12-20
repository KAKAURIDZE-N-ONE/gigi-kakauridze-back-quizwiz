<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/log-in', [AuthController::class, 'logIn']);
Route::post('/sign-up', [AuthController::class, 'signUp']);

Route::get('/user', function () {
    return response()->json(Auth::user());
})->middleware('auth:sanctum', 'verified:sanctum');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json([
      'message' => 'Email verified successfully!',
    ], 200);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
