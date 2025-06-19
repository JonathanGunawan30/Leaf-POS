<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFound;
use App\Http\Requests\CreatePurchaseRequest;
use App\Http\Requests\GeneratePOIssuanceRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Services\Interfaces\PurchaseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function store(CreatePurchaseRequest $request)
    {
        try {
            $data = $request->validated();
            $purchase = $this->purchaseService->store($data);
            $purchase->load(['supplier', 'user.role', 'details.product.unit']);
            return (new PurchaseResource($purchase))->additional([
                "message" => "Purchase created successfully",
                "statusCode" => 201
        ])->response()->setStatusCode(201);
        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function show ($id)
    {
        try {
            $purchase = $this->purchaseService->show($id);
            return (new PurchaseResource($purchase))->additional([
                "message" => "Purchase retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
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

    public function update(UpdatePurchaseRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $purchase = $this->purchaseService->update($data, $id);
            $purchase->load(['supplier', 'user.role']);
            return (new PurchaseResource($purchase))->additional([
                "message" => "Purchase updated successfully",
                "statusCode" => 200
            ]);
        } catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Purchase not found"
                ]
            ], 404);
        } catch (ProductNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (InsufficientStockException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        }
        catch (\Throwable $e ){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $request->get('per_page', 10);
            $purchases = $this->purchaseService->getAll();
            return (PurchaseResource::collection($purchases))->additional([
                "message" => "Purchase retrieved successfully",
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

    public function delete($id)
    {
        try {
            $this->purchaseService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Purchase deleted successfully"
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
            $data = $this->purchaseService->restore($id);
            $data->load(['user.role', 'details.product.unit', 'payments', 'taxes', 'supplier']);
            return (new PurchaseResource($data))->additional([
                "message" => "Purchase restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
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
            $request->get('per_page', 10);
            $purchase = $this->purchaseService->trashed();
            return (PurchaseResource::collection($purchase))->additional([
                "message" => "Purchase retrieved successfully",
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


    public function force($id)
    {
        try {
            $this->purchaseService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Purchase deleted successfully"
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
