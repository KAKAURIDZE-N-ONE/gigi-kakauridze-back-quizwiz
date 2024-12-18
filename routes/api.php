<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/api/log-in', [AuthController::class, 'logIn']);
Route::post('/api/sign-up', [AuthController::class, 'signUp']);

Route::get('/api/user', function () {
    return response()->json(Auth::user());
})->middleware('verified:sanctum');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect(env('FRONTEND_URL') . '/log-in');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
