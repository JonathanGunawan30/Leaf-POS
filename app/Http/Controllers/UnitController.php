<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use App\Services\Interfaces\UnitService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function store(CreateUnitRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $unit = Unit::create($data);

            return response()->json([
                "data" => new UnitResource($unit),
                "message" => "Unit created successfully",
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

    public function show($id)
    {
        try {
            $unit = $this->unitService->getDetail($id);
            return response()->json([
                "data" => new UnitResource($unit),
                "message" => "Unit retrieved successfully",
                "statusCode" => 200
            ]);
        }catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Unit not found"
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

    public function update(UpdateUnitRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $unit = $this->unitService->update($data, $id);
            return response()->json([
                "data" => new UnitResource($unit),
                "message" => "Unit updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Unit not found"
                ]
            ], 404);
        }
        catch (\Throwable $e){
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
            $units = $this->unitService->getAll();

            return (UnitResource::collection($units))
                ->additional([
                    'message' => 'Units retrieved successfully',
                    'statusCode' => 200,
                ]);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong'
                ]
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->unitService->deleteById($id);
            return response()->json([
                "data" => [
                    "message" => "Unit deleted successfully",
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Unit not found"
                ]
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong'
                ]
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $this->unitService->restore($id);
            return response()->json([
                'data' => [
                    'message' => 'Unit restored successfully'
                ],
                'statusCode' => 200,
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Unit not found"
                ]
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function force($id)
    {
        try {
            $this->unitService->force($id);
            return response()->json([
                "data" => [
                    "message" => "Unit deleted successfully"
                ],
                "statusCode" => 200,
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Unit not found"
                ]
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $trashedUnit = $this->unitService->trashed();
            return (UnitResource::collection($trashedUnit))->additional([
                "message" => "Unit retrieved successfully",
                "statusCode" => 200,
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
