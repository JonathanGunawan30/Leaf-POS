<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\Interfaces\SupplierService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function store(CreateSupplierRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $supplier = Supplier::create($data);
            return response()->json([
                "data" => new SupplierResource($supplier),
                "message" => "Supplier created successfully",
                "statusCode" => 201
            ], 201);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->getSupplierById($id);
            return response()->json([
                "data" => new SupplierResource($supplier),
                "message" => "Supplier retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Supplier not found"
                ]
            ], 404);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function update(UpdateSupplierRequest $request, $id): JsonResponse
    {
        try {
            $data= $request->validated();
            $supplier = $this->supplierService->updateSupplier($id, $data);
            return response()->json([
                "data" => new SupplierResource($supplier),
                "message" => "Supplier updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Supplier not found"
                ]
            ], 404);
        } catch (\Throwable $e){
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

            $perPage = $request->get("per_page", 10);
            if (!is_numeric($perPage) || (int)$perPage < 1) {
                return response()->json([
                    "errors" => [
                        "message" => [
                            "per_page" => "The per_page parameter must be a positive integer."
                        ]
                    ]
                ], 422);
            }

            $page = $request->get("page", 1);
            if (!is_numeric($page) || (int)$page < 1) {
                return response()->json([
                    "errors" => [
                        "message" => [
                            "page" => "The page parameter must be a positive integer."
                        ]
                    ]
                ], 422);
            }

            $filters = $request->only(["name", "company_name", "city", "country"]);

            $suppliers = $this->supplierService->getAll($filters, $perPage);

            return SupplierResource::collection($suppliers)
                ->additional([
                    "message" => "Suppliers retrieved successfully",
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

    public function delete($id): JsonResponse
    {
        try {
            $this->supplierService->deleteSupplier($id);
            return response()->json([
                "data" => [
                    "message" => "Supplier deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Supplier not found"
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ],400);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->restore($id);
            return response()->json([
                "data" => new SupplierResource($supplier),
                "message" => "Supplier restored successfully",
                "statusCode" => 200
            ]);
        }  catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Supplier not found"
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ],400);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function force($id): JsonResponse
    {
        try {
            $this->supplierService->force($id);
            return response()->json([
                "data" => [
                    "message" => "Supplier deleted successfully"
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Supplier not found"
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ],400);
        } catch (\Throwable $e){
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
            $trashedSUpplier = $this->supplierService->trashed();
            return (SupplierResource::collection($trashedSUpplier))->additional([
                "message" => "Suppliers retrieved successfully",
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
