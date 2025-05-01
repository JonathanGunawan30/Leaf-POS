<?php

namespace App\Http\Controllers;

use App\Exceptions\AddressNotFound;
use App\Exceptions\CourierIsBusyException;
use App\Exceptions\CourierNotFoundException;
use App\Exceptions\CustomerNotFound;
use App\Exceptions\DistanceNotFound;
use App\Exceptions\ProductNotFound;
use App\Exceptions\ShipmentNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Services\Interfaces\SaleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function store(CreateSaleRequest $request)
    {
        try {
            $data = $request->validated();
            $sale = $this->saleService->store($data);
            $sale->load(['customer', 'details.product.taxes', 'payments', 'shipments.courier', 'user.role', 'details.product.category', 'details.product.unit']);
            return (new SaleResource($sale))->additional([
                "message" => "Sale created successfully",
                "statusCode" => 201
            ])->response()->setStatusCode(201);
        } catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (CourierNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (CourierIsBusyException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 409);
        } catch (CustomerNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
                ]
            ], 404);
        }
        catch (StockNotEnoughException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        }
        catch (AddressNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Address not found"
                ]
            ], 404);
        } catch (DistanceNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Distance not found"
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

    public function show($id)
    {
        try {
            $sale = $this->saleService->show($id);
            $sale->load(['customer', 'details.product.taxes', 'payments', 'shipments.courier', 'user.role', 'details.product.category', 'details.product.unit']);
            return (new SaleResource($sale))->additional([
                "message" => "Sale retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Sale not found"
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

    public function update( UpdateSaleRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $sale = $this->saleService->update($data, $id);
            $sale->load(['customer', 'details.product.taxes', 'payments', 'shipments.courier', 'user.role', 'details.product.category', 'details.product.unit']);
            return (new SaleResource($sale))->additional([
                "message" => "Sale updated successfully",
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Sale not found"
                ]
            ], 404);
        } catch (ShipmentNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        }
        catch (CourierNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (CourierIsBusyException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 409);
        }
        catch (ProductNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Product not found"
                ]
            ], 404);
        } catch (CustomerNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Customer not found"
                ]
            ], 404);
        }catch (AddressNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Address not found"
                ]
            ], 404);
        } catch (DistanceNotFound $e){
            return response()->json([
                "errors" => [
                    "message" => "Distance not found"
                ]
            ], 404);
        }
        catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong" . $e->getMessage()
                ]
            ], 500);
        }
    }


}
