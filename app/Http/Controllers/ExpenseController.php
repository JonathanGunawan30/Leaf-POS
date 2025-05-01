<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpenseForceException;
use App\Exceptions\ExpenseRestoreException;
use App\Exports\ExpenseReportExport;
use App\Http\Requests\ExpenseReportExportRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseDetailResource;
use App\Http\Resources\ExpenseResource;
use App\Services\Interfaces\ExpenseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $data = $request->validated();
            $response = $this->expenseService->store($data);
            $response->load(["category", "user"]);
            return response()->json([
                "data" => new ExpenseResource($response),
                "message" => "Expense created successfully",
                "statusCode" => 201
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }

    }

    public function show($id): JsonResponse
    {
        try {
            $response = $this->expenseService->show($id);
            return response()->json([
                "data" => new ExpenseDetailResource($response)
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Expense not found"
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

    public function index(Request $request)
    {
        try {
            $expenses = $this->expenseService->getAll($request);

            return ExpenseDetailResource::collection($expenses);
        } catch (\Throwable $e) {
            return response()->json([
                "errors" => [
                    "message" => "Something went wrong"
                ]
            ], 500);
        }
    }

    public function update(UpdateExpenseRequest $request, $id): JsonResponse
    {
        try {
            $data = $request->validated();

            $expense = $this->expenseService->update($id, $data);

            return response()->json([
               "data" => new ExpenseResource($expense),
               "message" => "Expense updated successfully",
               "statusCode" => 200
            ]);

        } catch (ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Expense not found"
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

    public function delete($id)
    {
        try {
            $this->expenseService->delete($id);
            return response()->json([
               "data" => [
                   "message" => "Expense deleted successfully"
               ]
            ]);
        } catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Expense not found"
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

    public function restore($id)
    {
        try {
            $this->expenseService->restore($id);
            return response()->json([
               "data" => [
                   "message" => "Expense restored successfully"
               ]
            ]);
        } catch (ExpenseRestoreException $e) {
            return response()->json([
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ], $e->getCode());
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Expense not found"
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

    public function force($id)
    {
        try {
            $this->expenseService->force($id);
            return response()->json([
               "data" => [
                   "message" => "Expense hard delete successfully"
               ]
            ]);
        } catch(ExpenseForceException $e){
            return response()->json([
               "errors" => [
                   "message"=> $e->getMessage()
               ]
            ], $e->getCode());
        }
        catch(ModelNotFoundException $e){
            return response()->json([
                "errors" => [
                    "message" => "Expense not found"
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

    public function export(ExpenseReportExportRequest $request)
    {
        $filters = $request->validated();
        $format = $request->get('format', 'excel');
        $date = now()->format('d-m-Y');

        try {
            if (!in_array($format, ['pdf', 'excel'])) {
                return response()->json([
                    'errors' => [
                        'message' => 'Invalid export format'
                    ]
                ], 400);
            }

            if ($format === 'pdf') {
                $expenses = (new \App\Exports\ExpenseReportPdfExport($filters))->collection();

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.expense', [
                    'expenses' => $expenses,
                    'date' => $date
                ]);

                return $pdf->download("expense_report_{$date}.pdf");
            }

            $fileName = "expense_report_{$date}.xlsx";

            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\ExpenseReportExport($filters),
                $fileName
            );
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Failed to export expense report',
                ]
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $trashedExpenses = $this->expenseService->trashed();
            $trashedExpenses->load(["category", "user"]);
            return ExpenseResource::collection($trashedExpenses)->additional([
                "message" => "Trashed expenses retrieved successfully",
                "statusCode" => 200
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong',
                ]
            ], 500);
        }
    }


















}
