<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;
use App\Http\Resources\StockOpnameResource;
use App\Services\Interfaces\StockOpnameService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    protected StockOpnameService $stockOpnameService;

    public function __construct(StockOpnameService $stockOpnameService)
    {
        $this->stockOpnameService = $stockOpnameService;
    }

    public function store(CreateStockOpnameRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $stockOpname = $this->stockOpnameService->store($validatedData);

            return response()->json([
                "data" => new StockOpnameResource($stockOpname),
                "message" => "Stock opname created successfully",
                "statusCode" => 201
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        }
        catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong " . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $stockOpnames = $this->stockOpnameService->getAll($request);
            return (StockOpnameResource::collection($stockOpnames))->additional([
                "message" => "Stock opnames retrieved successfully",
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

    public function show($id)
    {
        try {
            $stockOpname = $this->stockOpnameService->show($id);
            return (new StockOpnameResource($stockOpname))->additional([
                "message" => "Stock opname retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Stock opname not found"
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

    public function update (UpdateStockOpnameRequest $request, $id)
    {
        try {
            $request->validated();
            $stockOpname = $this->stockOpnameService->update($request, $id);
            return (new StockOpnameResource($stockOpname))->additional([
                "message" => "Stock opname updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Stock opname not found"
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

    public function destroy($id)
    {
        try {
            $this->stockOpnameService->delete($id);
            return response()->json([
                'message' => 'Stock opname deleted successfully',
                'statusCode' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'errors' => ['message' => 'Stock opname not found']
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong']
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->stockOpnameService->forceDelete($id);
            return response()->json([
                'message' => 'Stock opname permanently deleted',
                'statusCode' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'errors' => ['message' => 'Stock opname not found']
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong']
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $this->stockOpnameService->restore($id);
            return response()->json([
                'message' => 'Stock opname restored successfully',
                'statusCode' => 200
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'errors' => ['message' => 'Stock opname not found']
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong']
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $trashed = $this->stockOpnameService->trashed();
            return StockOpnameResource::collection($trashed)->additional([
                'message' => 'Trashed stock opnames retrieved',
                'statusCode' => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['message' => 'Something went wrong']
            ], 500);
        }
    }

}
