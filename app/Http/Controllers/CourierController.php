<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Services\Interfaces\CourierService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    protected CourierService $courierService;

    public function __construct(CourierService $courierService)
    {
        $this->courierService = $courierService;
    }

    public function store(CreateCourierRequest $request)
    {
        try {
            $data = $request->validated();
            $courier = $this->courierService->store($data);
            return (new CourierResource($courier))->additional([
                "message" => "Courier created successfully",
                "statusCode" => 201
            ])->response()->setStatusCode(201);
        } catch(\Throwable $e){
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
            $courier = $this->courierService->show($id);
            return (new CourierResource($courier))->additional([
                "message" => "Courier retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Courier not found"
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

    public function update(UpdateCourierRequest $request, $id): JsonResponse|CourierResource
    {
        try {
            $data = $request->validated();
            $courier = $this->courierService->update($data, $id);
            return (new CourierResource($courier))->additional([
                "message" => "Courier updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Courier not found"
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

    public function index()
    {
        try {
            $courier = $this->courierService->getAll();
            return (CourierResource::collection($courier))->additional([
                "message" => "Courier retrieved successfully",
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
            $this->courierService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Courier deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Courier not found"
                ]
            ], 400);
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

    public function restore($id)
    {
        try {
            $courier = $this->courierService->restore($id);
            return (new CourierResource($courier))->additional([
                "message" => "Courier restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Courier not found"
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
            $this->courierService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Courier deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Courier not found"
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
            $couriers = $this->courierService->trashed();
            return CourierResource::collection($couriers)->additional([
                "message" => "Courier retrieved successfully",
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
