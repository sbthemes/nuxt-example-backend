<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function store()
    {
        $data = request()->validate([
            'name'      => ['required'],
            'email'     => ['required', 'email:filter', 'unique:App\Models\User,email'],
            'password'  => ['required', Password::min(8)],
        ]);

        $user = User::create([
            'name'      =>  $data['name'],
            'email'     =>  $data['email'],
            'password'  =>  bcrypt($data['password']),
        ]);

        $token = $user->createToken('web.provider');

        event(new Registered($user));

        return response()->json(['success' => 'Please check your email inbox (and spam) for an access link.']);
    }

    public function me()
    {
        return UserResource::make(auth()->user());
    }
}
