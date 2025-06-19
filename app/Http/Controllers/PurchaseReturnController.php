<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundInPurchaseException;
use App\Exceptions\ReturnedQuantityExceedsOriginalException;
use App\Http\Requests\CreatePurchaseReturnRequest;
use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Http\Resources\PurchaseReturnResource;
use App\Services\Interfaces\PurchaseReturnService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    protected PurchaseReturnService $purchaseReturnService;

    public function __construct(PurchaseReturnService $purchaseReturnService)
    {
        $this->purchaseReturnService = $purchaseReturnService;
    }

    public function store(CreatePurchaseReturnRequest $request)
    {
        try {
            $data = $request->validated();

            $result = $this->purchaseReturnService->create($data);
            $result->load(['purchase', 'user.role', 'supplier', 'details.product.unit', 'details.product.category', 'details.product.taxes', 'payments']);
            return (new PurchaseReturnResource($result))->additional([
                "message" => "Purchase returned successfully",
                "statusCode" => 201
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
                ]
            ], 404);
        } catch (ProductNotFoundInPurchaseException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (ReturnedQuantityExceedsOriginalException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        }
        catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => 'Something went wrong ' . $e->getMessage()
                ]
            ], 500);
        }

    }

    public function index(Request $request)
    {
        try {
            $request->get("per_page", 10);
            $purchase = $this->purchaseReturnService->getAll();
            return (PurchaseReturnResource::collection($purchase))->additional([
                "message" => "Purchase retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong "
                ]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $purchase = $this->purchaseReturnService->find($id);
            return (new PurchaseReturnResource($purchase))->additional([
                "message" => "Purchase retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
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

    public function update(UpdatePurchaseReturnRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $result = $this->purchaseReturnService->update($data, $id);
            return (new PurchaseReturnResource($result))->additional([
                "message" => "Purchase returned successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
                ]
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong " . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->purchaseReturnService->softDelete($id);
            return response()->json([
                "data" => [
                    "message" => "Purchase deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 400);
        }
        catch (\Throwable $e) {
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
            $data = $this->purchaseReturnService->restore($id);

            $data->load(['purchase', 'user.role', 'supplier', 'details.product.unit', 'details.product.category', 'details.product.taxes', 'payments']);

            return (new PurchaseReturnResource($data))->additional([
                "message" => "Purchase restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
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

    public function trashed(Request $request)
    {
        try {
            $request->get("per_page", 10);

            $purchaseReturn = $this->purchaseReturnService->trashed();
            return (PurchaseReturnResource::collection($purchaseReturn))->additional([
                "message" => "Purchase return retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e){
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
            $this->purchaseReturnService->hardDelete($id);
            return response()->json([
                "data" => [
                    "message"=> "Purchase return deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
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
}
