<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function logIn(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->hasVerifiedEmail()) {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'message' => 'Your email is not verified. Please verify your email to log in.'
                ], 400);
            }
        }

    }

    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        $token = $user->createToken('Verify Email', ['*'], Carbon::now()->addHours(2))->plainTextToken;

        $verificationUrl = 'http://127.0.0.1:5173/log-in?token=' . $token;

        Mail::raw("<a>$verificationUrl</a>", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Email Verification');
        });

        return response()->json([
            'message' => 'Please check your email for verification link.',
        ], 200);
    }
}
