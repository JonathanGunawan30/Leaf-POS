<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFound;
use App\Exceptions\SaleDetailNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Http\Requests\CreateSaleDetailRequest;
use App\Models\Product;
use App\Services\Interfaces\SaleDetailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SaleDetailController extends Controller
{
    protected SaleDetailService $saleDetailService;
    public function __construct(SaleDetailService $saleDetailService)
    {
        $this->saleDetailService = $saleDetailService;
    }


    public function store(CreateSaleDetailRequest $request, $saleId)
    {
        $data = $request->validated();

        try {
            $this->saleDetailService->store($data, $saleId);
            return response()->json([
                "data" => [
                    "message" => "Sale detail created successfully",
                ],
                "statusCode" => 201
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Sale id not found"
                ]
            ], 404);
        } catch (ProductNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (StockNotEnoughException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function delete($saleId, $saleDetailId)
    {
        try {
            $this->saleDetailService->delete($saleId, $saleDetailId);
            return response()->json([
                "data" => [
                    "message" => "Sale detail deleted successfully"
                ],
                "statusCode" => 200
            ]);
        }catch (ModelNotFoundException $e) {
            return response()->json([
                "errors" => [
                    "message" => "Sale id not found"
                ]
            ], 404);
        } catch (SaleDetailNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Sale detail not found"
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
}
