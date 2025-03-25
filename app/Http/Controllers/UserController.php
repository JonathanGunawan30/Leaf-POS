<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserResetPasswordRequest;
use App\Http\Requests\UserSendResetLinkRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\Interfaces\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->userService->register($data);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = $this->userService->login($data);

        return new UserResource($user);
    }

    public function get(): UserResource
    {
        $user = $this->userService->get();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $user = Auth::user();

        $data = $request->validated();

        if (empty($data)) {
            return new UserResource($user);
        }

        $updatedUser = $this->userService->update($user, $data);

        return new UserResource($updatedUser);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "message" => "Logged out successfully"
        ]);
    }

    public function sendResetLink(UserSendResetLinkRequest $request): JsonResponse
    {
        $request->validated();

        Password::sendResetLink($request->only('email'));

        return response()->json(['message' => 'Reset password link sent.'], 200);

    }

    public function resetPassword(UserResetPasswordRequest $request): JsonResponse
    {
        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset successfully.'], 200)
            : response()->json(['errors' => ['message' => 'Invalid token or email.']], 400);
    }

    public function changePassword(UserChangePasswordRequest $request): JsonResponse
    {
        $request->validated();

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => ['message' => 'Current password is incorrect.']
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }


}
