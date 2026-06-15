<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService {

    public function login(LoginRequest $request){
        
        $validated = $request->validated();
        $user = User::whereEmail($validated['email'])->first();

        if (empty($user) || ! Hash::check($validated['password'], $user->password)) {
            throw new Exception('Invalid credentials');
        }
        
        return [
            'token' => $user->createToken('api_token')->plainTextToken,
            'token_type'   => 'Bearer',
        ];
    }
}
