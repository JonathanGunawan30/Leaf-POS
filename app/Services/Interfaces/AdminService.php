<?php

namespace App\Services\Interfaces;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface AdminService
{
    function register (array $data): User;
    function getUserById($id): ?User;
    function updateUserById(array $data, $id): ?User;
    function deleteUserById($id): bool;
    public function getUsers(array $filters);
    public function addRole(array $roleName): Role;
    public function getRoleById($id): ?Role;
    public function getRoles(array $filters): LengthAwarePaginator;
    public function getUsersByRoleId(int $roleId, array $filters);
    public function updateUserRole(int $userId, int $newRoleId): array;
    public function updateStatus(int $id, string $status);
}
