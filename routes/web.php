<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::get('/api/user', function (Request $request) {
    return $request->user();
});
Route::post('/api/log-in', [AuthController::class, 'logIn']);
Route::post('/api/sign-up', [AuthController::class, 'signUp']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
