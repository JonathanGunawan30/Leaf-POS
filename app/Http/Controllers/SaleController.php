<?php

namespace App\Http\Controllers;

use App\Exceptions\AddressNotFound;
use App\Exceptions\CourierIsBusyException;
use App\Exceptions\CourierNotFoundException;
use App\Exceptions\CustomerNotFound;
use App\Exceptions\DistanceNotFound;
use App\Exceptions\ProductNotFound;
use App\Exceptions\RestoreException;
use App\Exceptions\SaleCannotBeDeletedException;
use App\Exceptions\SaleDetailNotFoundException;
use App\Exceptions\ShipmentNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Exports\SalesReportExport;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\SalesReportExportRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Customer;
use App\Models\Sale;
use App\Services\Interfaces\SaleService;
use App\Services\ShippingService;
use Barryvdh\DomPDF\Facade\Pdf;
use HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Nette\Schema\ValidationException;

class SaleController extends Controller
{
    protected SaleService $saleService;
    protected $shippingService;

    public function __construct(SaleService $saleService, ShippingService $shippingService)
    {
        $this->saleService = $saleService;
        $this->shippingService = $shippingService;
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
                    "message" => "Something went wrong "
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
        } catch (SaleDetailNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        } catch (StockNotEnoughException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
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
                    "message" => "Something went wrong "
                ]
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $request->get('per_page', 10);
            $sales = $this->saleService->getAll();
            return (SaleResource::collection($sales))->additional([
                "message" => "Sale retrieved successfully",
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
            $this->saleService->softdelete($id);
            return response()->json([
                "data" => [
                    "message" => "Sale deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors"=> [
                    "message" => "Sale id not found"
                ]
            ], 404);
        } catch (ValidationException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        } catch (SaleCannotBeDeletedException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 403);
        }
        catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong " . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function restore($id)
    {
        try {
            $sale = $this->saleService->restore($id);
            return (new SaleResource($sale))->additional([
                "message" => "Sale restored successfully",
                "statusCode" => "200"
            ]);
        }catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Sale id not found"
                ]
            ], 404);
        } catch (ValidationException $e){
            return response()->json([
                "errors" => [
                    "message" => "No stock available for any products. Please wait for stock replenishment."
                ]
            ], 403);
        } catch (RestoreException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 422);
        } catch (SaleCannotBeDeletedException $e){
            return response()->json([
                "errors" => [
                    "message" => $e->getMessage()
                ]
            ], 403);
        }
        catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong " . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $sales = $this->saleService->trashed();
            return (SaleResource::collection($sales))->additional([
                "message" => "Sale retrieved successfully",
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
            $this->saleService->harddelete($id);
            return response()->json([
                "data" => [
                    "message" => "Sale deleted successfully"
                ],
                "statusCode" => 200
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Sale id not found"
                ]
            ], 404);
        }catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function export(SalesReportExportRequest $request)
    {
        $filters = $request->validated();
        $format = $request->get('format', 'excel');
        $date = now()->format('d-m-Y');

        try {
            if (!in_array($format, ['excel', 'pdf'])) {
                return response()->json([
                    'errors' => ['message' => 'Invalid export format']
                ], 400);
            }

            // Untuk download excel, nanti otomatis kedownload
            if ($format === 'excel') {
                $fileName = "sales_report_{$date}.xlsx";
                return Excel::download(new SalesReportExport($filters), $fileName);
            }

            // Untuk testing simpan di local storage project
//            if ($format === 'excel') {
//                $fileName = "sales_report_{$date}.xlsx";
//                $path = "exports/{$fileName}";
//
//                Excel::store(new SalesReportExport($filters), $path, 'local');
//
//                return response()->json([
//                    'message' => 'Exported successfully',
//                    'path' => $path,
//                    'url' => asset("storage/exports/{$fileName}"),
//                ]);
//            }


            // Untuk download PDF, nanti otomatis kedownload
            if ($format === 'pdf') {
                $sales = (new \App\Exports\SalesReportPdfExport($filters))->collection();

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sale', [
                    'sales' => $sales,
                    'date' => $date,
                ]);

                return $pdf->download("sales_report_{$date}.pdf");
            }

            // Untuk testing simpan di local storage project
//            if ($format === 'pdf') {
//                $fileName = "sales_report_{$date}.pdf";
//                $path = "exports/{$fileName}";
//                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sale', [
//                    'sales' => (new \App\Exports\SalesReportPdfExport($filters))->collection(),
//                    'date' => $date,
//                ]);
//                $pdf->save("storage/app/private/exports/{$fileName}");
//                return response()->json([
//                    'message' => 'Exported successfully',
//                    'path' => $path,
//                    'url' => asset("storage/exports/{$fileName}"),
//                ]);
//            }

            return response()->json([
                'errors' => ['message' => 'PDF export not yet implemented']
            ], 501);


        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function deliveryNote($id)
    {
        try {
            $sale = Sale::with(['customer', 'details.product.taxes', 'payments', 'shipments.courier', 'user.role', 'details.product.category', 'details.product.unit'])
                ->findOrFail($id);
            $shipments = $sale->shipments->first();

            $pdf = Pdf::loadView('documents.delivery-note', [
                'sale' => $sale,
                'shipments' => $shipments,
            ]);

            // return $pdf->download('delivery-note-' . $sale->delivery_number . '.pdf');

            $fileName = 'delivery-note-' . $sale->delivery_number . '.pdf';
            $path = "exports/{$fileName}";

            Storage::put($path, $pdf->output());

            return $pdf->download('delivery-note-' . $sale->delivery_number . '.pdf');




        } catch (\Throwable $e){
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong". $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getShippingCost(Customer $customer)
    {
        try {
            $origin = env('SHIPPING_ORIGIN', 'Default Origin Address');
            $destination = $customer->address;

            $shippingDetails = $this->shippingService->getShippingDetails($origin, $destination);

            return response()->json($shippingDetails);
        } catch (\Exception $e) {
            Log::error("Error calculating shipping cost for customer {$customer->id}: " . $e->getMessage());
            return response()->json(['error' => 'Could not calculate shipping cost: ' . $e->getMessage()], 500);
        }
    }

}
