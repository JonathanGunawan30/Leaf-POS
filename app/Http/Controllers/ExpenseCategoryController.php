<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpenseCategoryNotFoundException;
use App\Http\Requests\CreateExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Http\Resources\CreateExpenseCategoryResource;
use App\Http\Resources\GetExpenseCategoryResource;
use App\Models\ExpenseCategory;
use App\Services\Interfaces\ExpenseCategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    protected $expenseCategoryService;

    public function __construct(ExpenseCategoryService $expenseCategoryService)
    {
        $this->expenseCategoryService = $expenseCategoryService;
    }

    public function createExpenseCategory(CreateExpenseCategoryRequest $request): JsonResponse|CreateExpenseCategoryResource
    {
        try {
            $categoryName = $request->validated();

            $category = ExpenseCategory::create($categoryName);

            return (new CreateExpenseCategoryResource($category))->additional([
                "message" => "Expense category created successfully",
                "statusCode" => 201
            ])->response()->setStatusCode(201);

        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

    public function getExpenseCategoryById($id)
    {
        try {
            $response = ExpenseCategory::findOrFail($id);
            return (new CreateExpenseCategoryResource($response))->additional([
                "message" => "Expense category retrieved successfully",
                "statusCode" => 200
            ]);
        }  catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Expense category not found"
                ]
            ], 404);
        }
        catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function updateExpenseCategory(UpdateExpenseCategoryRequest $request, int $id): JsonResponse|CreateExpenseCategoryResource
    {
        try {
            $categoryName = $request->validated();

            $category = $this->expenseCategoryService->update($id, $categoryName);

            return (new CreateExpenseCategoryResource($category))->additional([
                "message" => "Expense category updated successfully",
                "statusCode" => 200
            ]);
        } catch (ExpenseCategoryNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ]);
        }
    }

    public function getExpenseCategory(Request $request): JsonResponse|GetExpenseCategoryResource
    {
        try {
            $perPage = $request->query('per_page', 10);
            $search = $request->query('search');

            if ($perPage < 1 || $perPage > 100) {
                return response()->json([
                    'errors' => [
                        'message' => 'per_page must be between 1 and 100'
                    ]
                ], 422);
            }

            $categories = ExpenseCategory::when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
                ->paginate($perPage);

            return GetExpenseCategoryResource::collection($categories)->additional([
                "message" => "Expense category retrieved successfully",
                "statusCode" => 200
            ])->response()->setStatusCode(200);

        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ],
                "statusCode" => 500
            ], 500);
        }
    }

    public function deleteExpenseCategory($id): JsonResponse
    {
        try {
            $this->expenseCategoryService->deleteExpenseCategory((int) $id);

            return response()->json([
                "data" => [
                    "message" => "Expense category deleted successfully"
                ]
            ], 200);

        } catch (ExpenseCategoryNotFoundException $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function restoreExpenseCategory($id)
    {
        try {
            $this->expenseCategoryService->restoreExpenseCategory((int) $id);
            return response()->json([
                "data" => [
                    "message" => "Expense category restored successfully"
                ]
            ], 200);
        } catch (ExpenseCategoryNotFoundException $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        } catch (\Throwable $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function forceExpenseCategory($id)
    {
        try {
            $this->expenseCategoryService->forceDeleteExpenseCategory((int)$id);

            return response()->json([
                'data' => [
                    'message' => 'Expense category permanently deleted'
                ]
            ]);
        }catch (ExpenseCategoryNotFoundException $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        } catch(\Exception $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e){
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $trashedCategoryExpense = $this->expenseCategoryService->trashed();
            return GetExpenseCategoryResource::collection($trashedCategoryExpense)->additional([
                "message" => "Expense category trashed successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => "Something went wrong"
                ]
            ], 500);
        }
    }
}
