<?php

namespace App\Services\Implementations;

use App\Models\Role;
use App\Models\User;
use App\Services\Interfaces\AdminService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminServiceImpl implements AdminService
{

    function register(array $data): User
    {
        $data["password"] = Hash::make($data["password"]);
        $data["status"] = "active";
        return User::query()->create($data);
    }

    function getUserById($id): ?User
    {
        return User::find($id);
    }

    function updateUserById(array $data, $id): ?User
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        if(isset($data["password"])){
            $data["password"] = Hash::make($data["password"]);
        }

        $user->fill($data);

        if($user->isDirty()){
            $user->save();
        }

        return $user;
    }

    function deleteUserById($id): bool
    {
        $authUser = auth()->user();

        $user = User::find($id);
        if (!$user) {
            return false;
        }

        if ($authUser && $authUser->id === $user->id) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'message' => 'You cannot delete your own account.'
            ]);
        }

        $user->delete();
        return true;
    }


    public function getUsers(array $filters)
    {
        $query = User::query();

        if (!empty($filters["role_id"])) {
            $query->where("role_id", $filters["role_id"]);
        }

        if (!empty($filters["search"])) {
            $query->where("name", "LIKE", "%" . $filters["search"] . "%")
                ->orWhere("email", "LIKE", "%" . $filters["search"] . "%");
        }

        return $query->paginate($filters["per_page"] ?? 10);
    }

    public function addRole(array $roleName): Role
    {
        return Role::query()->create($roleName);
    }

    public function getRoleById($id): ?Role
    {
        return Role::find($id);
    }
    public function getRoles(array $filters): LengthAwarePaginator
    {
        $perPage = isset($filters['per_page']) ? min((int) $filters['per_page'], 100) : 10;

        $query = Role::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function getUsersByRoleId(int $roleId, array $filters)
    {
        $query = User::query()
            ->where("role_id", $roleId);

        if (!empty($filters["search"])) {
            $search = $filters["search"];
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%$search%")
                    ->orWhere("email", "like", "%$search%");
            });
        }

        $perPage = $filters["per_page"] ?? 10;
        $perPage = min(max((int) $perPage, 1), 100);

        return $query->paginate($perPage);
    }
    public function updateUserRole(int $userId, int $newRoleId): array
    {
        $user = User::find($userId);
        if (!$user) {
            throw new ModelNotFoundException("User not found");
        }

        $currentRoleId = $user->role_id;
        $adminRoleId = Role::where('name', 'Admin')->value('id');

        if ($currentRoleId === $adminRoleId && $newRoleId !== $currentRoleId) {
            $adminCount = User::where('role_id', $adminRoleId)->count();

            if ($adminCount <= 1) {
                return [
                    'status' => 'fail',
                    'message' => 'At least one user must have the Admin role.'
                ];
            }
        }

        $user->role_id = $newRoleId;
        $user->save();

        return [
            'status' => 'success',
            'user' => $user
        ];
    }

    public function updateStatus(int $id, string $status)
    {
        $authUser = auth()->user();
        $user = User::find($id);

        if (!$user) {
            throw new ModelNotFoundException('User not found');
        }

        if ($authUser->role->name !== 'Admin') {
            throw new AuthorizationException('Only admins can change user status.');
        }

        if ($user->id === $authUser->id && $status === 'inactive') {
            throw ValidationException::withMessages([
                'message' => 'You cannot deactivate your own account.'
            ]);
        }


        $user->status = $status;
        $user->save();
        return $user->refresh();
    }
}
