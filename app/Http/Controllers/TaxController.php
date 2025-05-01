<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use App\Services\Interfaces\TaxService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxController extends Controller
{
    protected $taxService;

    public function __construct(TaxService $taxService)
    {
        $this->taxService = $taxService;
    }

    public function store(CreateTaxRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $tax = Tax::create($data);
            return response()->json([
                "data" => new TaxResource($tax),
                "message" => "Tax created successfully",
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

    public function show($id): JsonResponse
    {
        try {
            $data = $this->taxService->getTaxById($id);
            return response()->json([
                "data" => new TaxResource($data),
                "message" => "Tax retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Tax not found"
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

    public function update($id, UpdateTaxRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $tax = $this->taxService->updateTax($id, $request);
            return response()->json([
                "data" => new TaxResource($tax),
                "message" => "Tax updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Tax not found"
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
            $taxes = $this->taxService->getAll();
            return (TaxResource::collection($taxes))
                ->additional([
                    "message" => "Taxes retrieved successfully",
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
            $this->taxService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Tax deleted successfully",
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Tax not found"
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
            $tax = $this->taxService->restore($id);
            return response()->json([
                "data" => new TaxResource($tax),
                "message" => "Tax restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Tax not found"
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
            $tax = $this->taxService->hardDelete($id);
            return response()->json([
                "data" => new TaxResource($tax),
                "message" => "Tax deleted successfully",
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Tax not found"
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
            $trashedTaxes = $this->taxService->trashed();
            return (TaxResource::collection($trashedTaxes))->additional([
                "message" => "Taxes retrieved successfully",
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
