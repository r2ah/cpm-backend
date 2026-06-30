<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Fortify\PasswordValidationRules;

use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;

use Illuminate\Validation\ValidationException;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) : JsonResponse
{
    $users = User::latest()->get();

    return response()->json([
        'success' => true,
        'data' => UserResource::collection($users)
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
 

public function store(UserRequest $request)
{
    $validated = $request->validated();

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'phone' => $validated['phone'] ?? null,
    ]);

    if (!empty($validated['role'])) {
    $user->syncRoles([$validated['role']]);
     }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'data' => new UserResource($user),
        'token' => $token
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(User $user) : JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(UserRequest $request, User $user)
{
    $validated = $request->validated();

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
    ]);

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
        $user->save();
    }

    if (!empty($validated['role'])) {
    $user->syncRoles([$validated['role']]);
    }

    return response()->json([
        'success' => true,
        'data' => new UserResource($user)
    ]);
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) : JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}
