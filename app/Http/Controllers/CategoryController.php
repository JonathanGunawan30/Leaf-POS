<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Interfaces\CategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $category = Category::create($data);
            return response()->json([
                "data" => new CategoryResource($category),
                "message" => "Category created successfully",
                "statusCode" => 201
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $data = $this->categoryService->getCategoryById($id);
            return response()->json([
                "data" => new CategoryResource($data),
                "message" => "Category retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Category not found"
                ]
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $data = $this->categoryService->updateCategory($id, $request->validated());
            return response()->json([
                "data" => new CategoryResource($data),
                "message" => "Category updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Category not found"
                ]
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        try {
            $request->get('per_page', 10);
            $categories = $this->categoryService->getAll();

            return (CategoryResource::collection($categories))->additional([
                "message" => "Categories retrieved successfully",
                "statusCode" => 200
            ]);
        }catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->categoryService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Category deleted successfully",
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Category not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $data = $this->categoryService->restore($id);
            return response()->json([
                "data" => new CategoryResource($data),
                "message" => "Category restored successfully",
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Category not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function force($id)
    {
        try {
            $this->categoryService->force($id);
            return response()->json([
                "data" => [
                    "message" => "Category deleted successfully"
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Category not found"
                ]
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $trashedCategories = $this->categoryService->trashed();
            return (CategoryResource::collection($trashedCategories))->additional([
                "message" => "Categories retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }
}
