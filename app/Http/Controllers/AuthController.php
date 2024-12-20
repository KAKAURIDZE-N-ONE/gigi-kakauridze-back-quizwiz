<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function logIn(LoginRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::guard('web')->attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        Auth::user();

        return response()->json([
            'message' => 'Login successful.',
        ], 200);
    }

    public function signUp(SignUpRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        Auth::user();
        Auth::login($user);
        $request->session()->regenerate();

        $user->notify(new CustomVerifyEmail($user));

        return response()->json([
            'message' => 'Please check your email for verification link.',
        ], 200);
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return response()->json([
          'message' => 'Email verified successfully!',
        ], 200);
    }

    public function verifyNotfication(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }

    public function getUser()
    {
        return response()->json(Auth::user());
    }
}
