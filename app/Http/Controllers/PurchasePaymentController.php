<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePurchasePaymentRequest;
use App\Http\Requests\UpdatePurchasePaymentRequest;
use App\Http\Resources\PurchasePaymentResource;
use App\Http\Resources\PurchaseResource;
use App\Services\Interfaces\PurchasePaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PurchasePaymentController extends Controller
{
    protected PurchasePaymentService $purchasePaymentService;

    public function __construct(PurchasePaymentService $purchasePaymentService)
    {
        $this->purchasePaymentService = $purchasePaymentService;
    }

    public function store(CreatePurchasePaymentRequest $request)
    {
        try {
            $data = $request->validated();
            $payment = $this->purchasePaymentService->create($data);
            return (new PurchasePaymentResource($payment))->additional([
                "message" => "Payment created successfully",
                "statusCode" => 201
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], $e->getCode() ?? 404);
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
            $data = $this->purchasePaymentService->show($id);
            return (new PurchasePaymentResource($data))->additional([
                "message" => "Payment retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Payment not found"
                ]
            ],404);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }


    public function update(UpdatePurchasePaymentRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $purchase = $this->purchasePaymentService->update($data, $id);
            return (new PurchasePaymentResource($purchase))->additional([
                "message" => "Payment updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Payment not found"
                ]
            ],404);
        } catch (\Throwable $e) {
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
            $purchase = $this->purchasePaymentService->getAll();
            return (PurchasePaymentResource::collection($purchase))->additional([
                "message" => "Payment retrieved successfully",
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

    public function delete($id)
    {
        try {
            $this->purchasePaymentService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Payment deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Payment not found"
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

    public function restore($id)
    {
        try {
            $purchase = $this->purchasePaymentService->restore($id);
            return (new PurchasePaymentResource($purchase))->additional([
                "message" => "Payment restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Payment not found"
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

    public function force($id)
    {
        try {
            $this->purchasePaymentService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Payment deleted successfully"
                ],
                'statusCode' => 200,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Payment not found"
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

    public function trashed()
    {
        try {
            $payments = $this->purchasePaymentService->trashed();
            return PurchasePaymentResource::collection($payments)->additional([
                "message" => "Payments retrieved successfully",
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

}
