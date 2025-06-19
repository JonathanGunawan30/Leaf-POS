<?php

namespace App\Services\Implementations;

use App\Models\User;
use App\Services\Interfaces\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserServiceImpl implements UserService
{

    function register(array $data): User
    {
        $data["password"] = Hash::make($data["password"]);
        return User::create($data);
    }

    function login(array $data): Authenticatable
    {
//        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
//            throw new HttpResponseException(response([
//                "errors" => [
//                    "message" => ["Email or password is wrong"]
//                ]
//            ], 401));
//        }

        if (!$token = JWTAuth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            throw new HttpResponseException(response([
                "errors" => ["message" => ["Email or password is wrong"]]
            ], 401));
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Your account is inactive. Please contact the administrator."]
                ]
            ], 403));
        }

//        $token = $user->createToken('X-API-Token')->plainTextToken;

        $user->token = $token;
        return $user;

    }

    function get(): Authenticatable
    {
        $user = Auth::user();

        if (!$user) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Unauthorized"]
                ]
            ], 401));
        }

        if ($user->status !== 'active') {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => ["Your account is inactive. Please contact the administrator."]
                ]
            ], 403));
        }

        return $user;
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);

        if ($user->isDirty()) {
            $user->save();
        }

        return $user;
    }


}
