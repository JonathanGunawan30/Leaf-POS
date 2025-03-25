<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface UserService
{
    function register(array $data): User;
    function login(array $data): Authenticatable;
    function get(): Authenticatable;
    function update(User $user, array $data): User;
}
