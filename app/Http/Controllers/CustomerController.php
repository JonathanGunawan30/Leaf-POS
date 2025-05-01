<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\Interfaces\CustomerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function store(CreateCustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $customer = $this->customerService->create($data);
            return (new CustomerResource($customer))->additional([
                "message" => "Customer created successfully",
                "statusCode" => 201
            ])->response()->setStatusCode(201);
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
            $customer = $this->customerService->show($id);
            return (new CustomerResource($customer))->additional([
                "message" => "Customer retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
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

    public function update (UpdateCustomerRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $customer = $this->customerService->update($data, $id);
            return (new CustomerResource($customer))->additional([
                "message" => "Customer updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
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
            $customer = $this->customerService->getAll();
            return (CustomerResource::collection($customer))->additional([
                "message" => "Customer retrieved successfully",
                "statusCode" => 200
            ]);

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

    public function delete($id): JsonResponse
    {
        try {
            $this->customerService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Customer deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
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

    public function restore($id): JsonResponse|CustomerResource
    {
        try {
            $customer = $this->customerService->restore($id);
            return (new CustomerResource($customer))->additional([
                "message" => "Customer restored successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
                ]
            ], 404);
        } catch(\Exception $e){
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

    public function force($id)
    {
        try {
            $this->customerService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Customer deleted successfully"
                ],
                "statusCode" =>200
            ]);
        }catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
                ]
            ], 404);
        } catch(\Exception $e){
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

    public function trashed()
    {
        try {
            $customers = $this->customerService->trashed();
            return CustomerResource::collection($customers)->additional([
                "message" => "Customer retrieved successfully",
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
