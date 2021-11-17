<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login()
    {
        $data = request()->validate([
            'email'     =>  ['required', 'email:filter'],
            'password'  =>  ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            throw ValidationException::withMessages(['Invalid email or password.']);
        }

        $user->makeVisible(['password']);

        Hash::check($data['password'], $user->password) ?:
            throw ValidationException::withMessages(['Invalid email or password.']);

        $token = $user->createToken('web.provider');

        return [
            'token' =>  $token->plainTextToken,
        ];
    }

    public function verifyEmail()
    {
        if (!request()->hasValidSignature()) {
            throw ValidationException::withMessages(['Invalid verification link.']);
        }

        if (!hash_equals((string) request()->route('id'), (string) auth()->user()->getKey())) {
            throw ValidationException::withMessages(['Invalid verification links.']);
        }

        if (!hash_equals((string) request()->route('hash'), sha1(request()->user()->getEmailForVerification()))) {
            throw ValidationException::withMessages(['Invalid verification link.']);
        }

        if (!request()->user()->hasVerifiedEmail()) {
            if (request()->user()->markEmailAsVerified()) {
                event(new Verified(request()->user()));
            } else {
                throw ValidationException::withMessages(['Invalid verification link.']);
            }
        }

        return response()->noContent();
    }

    public function resendEmailVerificationLink()
    {
        auth()->user()->sendEmailVerificationNotification();
        return response()->json(['success' => 'Please check your email inbox (and spam) for an access link.']);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
