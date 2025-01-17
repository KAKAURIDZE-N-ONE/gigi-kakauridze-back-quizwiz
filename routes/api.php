<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/log-in', [AuthController::class, 'logIn']);
Route::post('/log-out', [AuthController::class, 'logout']);
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

Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('guest');

Route::post('/email/verification-notification', [AuthController::class, 'verifyNotfication'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/quizzes', [QuizController::class, 'getQuizzes']);

Route::get('/quizzes/count', [QuizController::class, 'getQuizzesQuantity']);

Route::get('/quizzes/{quiz}', [QuizController::class, 'getQuiz']);

Route::post('/quizzes/{quiz}', [QuizController::class, 'submitQuiz']);

Route::get('/categories', [CategoryController::class, 'getCategories']);

Route::get('/categories/count', [CategoryController::class, 'getCategoriesLength']);

Route::get('/levels', [LevelController::class, 'getLevels']);
