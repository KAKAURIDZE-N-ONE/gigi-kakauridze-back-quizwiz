<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

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

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $frontendUrl = env('FRONTEND_URL', 'http://127.0.0.1:5173'); // Default fallback if .env value is missing
        $customVerificationUrl = str_replace(
            'http://127.0.0.1:8000',
            $frontendUrl,
            $verificationUrl
        );

        $user->notify(new CustomVerifyEmail($customVerificationUrl));

        return response()->json([
            'message' => 'Please check your email for verification link.',
        ], 200);
    }
}
