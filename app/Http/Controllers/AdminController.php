<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminAddUserRoleRequest;
use App\Http\Requests\AdminRegisterUserRequest;
use App\Http\Requests\AdminUpdateRoleRequest;
use App\Http\Requests\AdminUpdateUserRequest;
use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\GetUsersByRoleRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\Interfaces\AdminService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Throwable;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function register(AdminRegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->adminService->register($data);

        return (new AdminResource($user))->response()->setStatusCode(201);
    }

    public function getUserById($id): AdminResource|JsonResponse
    {
        $user = $this->adminService->getUserById($id);

        if (!$user) {
            return response()->json([
                "errors" => ["message" => "User not found"]
            ], 404);
        }

        return new AdminResource($user);
    }

    public function updateUserById(AdminUpdateUserRequest $request, $id): AdminResource|JsonResponse
    {
        try {
            $data = $request->validated();

            $user = $this->adminService->updateUserById($data, $id);

            if (!$user) {
                return response()->json([
                    "errors" => ["message" => "User not found"]
                ], 404);
            }

            return new AdminResource($user);

        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong']
            ], 500);
        }
    }


    public function deleteUserById($id): JsonResponse
    {
        try {
            $deleted = $this->adminService->deleteUserById($id);

            if (!$deleted) {
                return response()->json([
                    "errors" => [
                        "message" => "User not found"
                    ]
                ], 404);
            }

            return response()->json([
                "data" => [
                    "message" => "User deleted successfully"
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->errors()['message'] ?? ['Invalid request']
                ]
            ], 400);
        }
    }


    public function restoreUserById($id)
    {
        try {
            $user = User::withTrashed()->find($id);

            if (!$user) {
                return response()->json([
                    "errors" => ["message" => "User not found"]
                ], 404);
            }

            if (!$user->trashed()) {
                return response()->json([
                    "errors" => ["message" => "User is not deleted"]
                ], 400);
            }

            $user->restore();

            return response()->json([
                "data" => [
                    "message" => "User restored successfully"
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "errors" => ["message" => "Something went wrong"]
            ], 500);
        }
    }

    public function forceDeleteUserById($id)
    {
        try {
            $user = User::withTrashed()->find($id);

            if (!$user) {
                return response()->json([
                    "errors" => ["message" => "User not found"]
                ], 404);
            }

            $user->forceDelete();

            return response()->json([
                "data" => [
                    "message" => "User permanently deleted"
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "errors" => ["message" => "Something went wrong"]
            ], 500);
        }
    }


    public function getUsers(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(["role_id", "search", "per_page"]);
        $users = $this->adminService->getUsers($filters);

        return AdminResource::collection($users);
    }

    public function addRole(AdminAddUserRoleRequest $request): JsonResponse
    {
        try {
            $roleName = $request->validated();

            $role = $this->adminService->addRole($roleName);

            return (new RoleResource($role))->response()->setStatusCode(201);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => ["message" => "Something went wrong"]
            ], 500);
        }
    }

    public function getRoleById($id): RoleResource|JsonResponse
    {
        try {
            $role = $this->adminService->getRoleById($id);

            if (!$role) {
                return response()->json([
                    "errors" => ["message" => "Role not found"]
                ], 404);
            }

            return new RoleResource($role);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => ["message" => "Something went wrong"]
            ], 500);
        }
    }


    public function updateRoleById(AdminUpdateRoleRequest $request, $id): RoleResource|JsonResponse
    {
        try {
            $role = Role::find($id);

            if(!$role){
                return response()->json([
                    "errors" => [
                        "message" => "Role not found"
                    ]
                ], 404);
            }

            if($role->name === "Admin"){
                return response()->json([
                    "errors" => [
                        "message" => "Cannot update Admin role, it is a system role"
                    ]
                ], 400);
            }

            $role->update($request->validated());

            return new RoleResource($role);
        } catch (Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function deleteRoleById($id): JsonResponse
    {
        try {
            $role = Role::find($id);

            if(!$role){
                return response()->json([
                    "errors" => [
                        "message" => "Role not found"
                    ]
                ], 404);
            }

            if($role->name === "Admin"){
                return response()->json([
                    "errors" => [
                        "message" => "Cannot delete Admin role, it is a system role"
                    ]
                ], 400);
            }

            if (User::where('role_id', $id)->exists()) {
                return response()->json([
                    "errors" => [
                        "message" => "Cannot delete role, there are users assigned to this role."
                    ]
                ], 400);
            }

            $role->delete();

            return response()->json([
                "data" => [
                    "message" => "Role deleted successfully."
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function restoreRoleById($id): RoleResource|JsonResponse
    {
        try {
            $role = Role::withTrashed()->find($id);

            if(!$role){
                return response()->json([
                    "errors" => [
                        "message" => "Role not found"
                    ]
                ], 404);
            }

            if(!$role->trashed()){
                return response()->json([
                    "errors" => [
                        "message" => "Role is not deleted"
                    ]
                ], 400);
            }

            $role->restore();

            return new RoleResource($role);
        } catch (Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function forceDeleteRoleById($id): JsonResponse
    {
        try {
            $role = Role::withTrashed()->find($id);

            if(!$role){
                return response()->json([
                    "errors" => [
                        "message" => "Role not found"
                    ]
                ], 404);
            }

            if($role->name === "Admin"){
                return response()->json([
                    "errors" => [
                        "message" => "Cannot delete Admin role, it is a system role"
                    ]
                ], 400);
            }

            if(!$role->trashed()){
                return response()->json([
                    "errors" => [
                        "message" => "Role is not deleted"
                    ]
                ], 400);
            }

            if (User::where('role_id', $id)->exists()) {
                return response()->json([
                    "errors" => [
                        "message" => "Cannot delete role, there are users assigned to this role."
                    ]
                ], 400);
            }

            $role->forceDelete();

            return response()->json([
                "data" => [
                    "message" => "Force delete role successfully."
                ]
            ]);
        } catch (Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getRoles(Request $request)
    {
        try {
            $filters = $request->only(["role_id", "search", "per_page"]);
            $roles = $this->adminService->getRoles($filters);

            return RoleResource::collection($roles);
        } catch (\Exception $e) {
            return response()->json([
                "errors" => [
                    "message" => "Failed to get roles: " . $e->getMessage()
                ]
            ], 500);
        }
    }
    public function getUsersByRoleId($id, GetUsersByRoleRequest $request)
    {
        $request->validated();

        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                "errors" => [
                    "message" => "Role not found"
                ]
            ], 404);
        }

        try {
            $filters = $request->only(["search", "per_page"]);
            $users = $this->adminService->getUsersByRoleId($id, $filters);

            return UserResource::collection($users);
        } catch (\Exception $e) {
            return response()->json([
                "errors" => [
                    "message" => "Failed to get users: " . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function updateUserRole(ChangeRoleRequest $request, $id)
    {
        $request->validated();

        try {
            $result = $this->adminService->updateUserRole($id, $request->role_id);

            if ($result['status'] === 'fail') {
                return response()->json([
                    'errors' => [
                        'message' => $result['message']
                    ]
                ], 422);
            }

            return response()->json([
                'data' => [
                    'message' => 'User role updated successfully.',
                    'user' => new UserResource($result['user'])
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'errors' => ['message' => 'User not found']
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        try {
            $request->validated();

            $user = $this->adminService->updateStatus((int) $id, $request->status);

            return response()->json([
                'data' => [
                    'message' => 'User status updated successfully.',
                    'user' => new UserResource($user)
                ]
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'errors' => [
                    'message' => 'User not found'
                ]
            ], 404);

        } catch (AuthorizationException $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 403);

        } catch (ValidationException $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->errors()['message'][0] ?? 'Validation error.'
                ]
            ], 400);

        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Unexpected error occurred.',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }




}
