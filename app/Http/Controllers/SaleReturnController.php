<?php

namespace App\Http\Controllers;

use App\Exceptions\CourierNotFoundException;
use App\Exceptions\ProductNotFoundInSaleException;
use App\Exceptions\ReturnedQuantityExceedsOriginalException;
use App\Http\Requests\CreateSaleReturnRequest;
use App\Http\Resources\SaleReturnResource;
use App\Services\Interfaces\SaleReturnService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    protected SaleReturnService $saleReturnService;

    public function __construct(SaleReturnService $saleReturnService)
    {
        $this->saleReturnService = $saleReturnService;
    }

    public function store(CreateSaleReturnRequest $request)
    {
        try {
            $data = $request->validated();
            $response = $this->saleReturnService->store($data);
            return (new SaleReturnResource($response))->additional([
                "message" => "Sale returned successfully",
                "statusCode" => 201
            ])->response()->setStatusCode(201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Sale not found"
                ]
            ], 404);
        } catch (ProductNotFoundInSaleException $e){
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
        } catch (CourierNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                "errors" => [
                    "message" => 'Something went wrong'
                ]
            ], 500);
        }
    }


}
