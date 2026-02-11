<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{

    public function loginValidationRules()
    {
        return array(
            'email' => 'required|email',
            'password' => 'required|string'
        );
    }

    public function logoutValidationRules()
    {
        return array(
            'email' => 'required|string'
        );
    }

    public function getCurrentUser()
    {
        $user = Auth::user();
        return $user;
    }

    public function createUserToken($user)
    {
        return $user->createToken('costech')->plainTextToken;
    }

    public function login($email, $password)
    {
        $credentials = array(
            'email' => $email,
            'password' => $password
        );

        $loginUser = Auth::attempt($credentials);

        return $loginUser;
    }
}
