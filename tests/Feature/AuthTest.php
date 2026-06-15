<?php

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;

test('user can login with valid credentials', function () {
    
    $requestBody = createRequest(LoginRequest::class, [
        'email' => 'm_akkurt@live.com',
        'password' => 'password'
    ]);

    $authService = (new AuthService())->login($requestBody);
    expect($authService)->token->toBeString();
    expect($authService)->token_type->toBeString();
    expect($authService)->token_type->toBe('Bearer');
});

test('user can not login with invalid credentials', function () {
    
    $requestBody = createRequest(LoginRequest::class, [
        'email' => 'm_akkurt@12345.com',
        'password' => 'password'
    ]);
    
    (new AuthService())->login($requestBody);

})->throws(Exception::class, 'Invalid credentials');

test('user can not login with fake email even thought it already exist in database', function () {
    
    $email = 'example1@example.com';
    
    User::factory(['email' => $email])->create();
   
    $requestBody = createRequest(LoginRequest::class, [
        'email' => $email,
        'password' => 'password'
    ]);
    
    (new AuthService())->login($requestBody);
})->throws(Exception::class, 'The email must be a valid email address.');

test('user can not login with empty request', function () {

    $requestBody = createRequest(LoginRequest::class, []);
    
    (new AuthService())->login($requestBody);
    
})->throws(Exception::class);

test('user can not login without email', function () {
    
    $requestBody = createRequest(LoginRequest::class, [
        'email' => '',
        'password' => 'password'
    ]);
    
    (new AuthService())->login($requestBody);

})->throws(Exception::class, "The email field is required.");

test('user can not login without password', function () {
    
    $requestBody = createRequest(LoginRequest::class, [
        'email' => 'm_akkurt@live.com',
        'password' => ''
    ]);
    
    (new AuthService())->login($requestBody);

})->throws(Exception::class, "The password field is required.");
