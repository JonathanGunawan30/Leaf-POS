<?php

namespace App\Http\Controllers;

use App\Events\StockAlertEvent;
use App\Mail\StockAlertMail;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class StockAdjustmentController extends Controller
{
    /**
     * Store a newly created stock adjustment in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'previous_stock' => 'required|integer|min:0',
                'new_stock' => 'required|integer|min:0',
                'notes' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = Product::findOrFail($request->product_id);

            if ($product->stock != $request->previous_stock) {
                return response()->json([
                    'errors' => [
                        'message' => 'The product stock has changed since you loaded the page. Please refresh and try again.'
                    ]
                ], 409);
            }

            $adjustment = $request->new_stock - $request->previous_stock;

            return DB::transaction(function () use ($request, $product, $adjustment) {
                $stockAdjustment = StockAdjustment::create([
                    'product_id' => $request->product_id,
                    'user_id' => auth()->id(),
                    'previous_stock' => $request->previous_stock,
                    'new_stock' => $request->new_stock,
                    'adjustment' => $adjustment,
                    'notes' => $request->notes
                ]);

                $product->stock = $request->new_stock;
                $product->save();


                if ($product->stock <= $product->stock_alert) {
                    $notificationService = new NotificationService();

                    $notification = $notificationService->createStockAlert($product, null, false);

                    event(new StockAlertEvent($product, $notification));

                    $users = User::whereHas('role', function ($query) {
                        $query->whereIn('name', ['Admin', 'Purchasing', 'Inventory']);
                    })->get();

                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new StockAlertMail($product));
                    }
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $stockAdjustment->id,
                    'previous_stock' => $request->previous_stock,
                    'new_stock' => $request->new_stock,
                    'quantity' => $adjustment,
                    'movement_type' => 'adjustment',
                    'notes' => $request->notes
                ]);

                return response()->json([
                    'data' => $stockAdjustment,
                    'message' => 'Stock adjustment created successfully',
                    'statusCode' => 201
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display a listing of the stock adjustments.
     */
    public function index(Request $request)
    {
        try {
            $query = StockAdjustment::with(['product', 'user'])
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($query) use ($search) {
                        $query->whereHas('product', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                        })
                        ->orWhere('notes', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('product_id'), function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                })
                ->when($request->filled('start_date'), function ($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->filled('end_date'), function ($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->end_date);
                });

            $sortField = $request->sort_field ?? 'created_at';
            $sortOrder = $request->sort_order ?? 'desc';
            $query->orderBy($sortField, $sortOrder);

            $perPage = $request->per_page ?? 10;
            $stockAdjustments = $query->paginate($perPage);

            return response()->json([
                'data' => $stockAdjustments,
                'message' => 'Stock adjustments retrieved successfully',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Something went wrong: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show($id)
    {
        try {
            $stockAdjustment = StockAdjustment::with(['product', 'user'])->findOrFail($id);

            return response()->json([
                'data' => $stockAdjustment,
                'message' => 'Stock adjustment retrieved successfully',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Stock adjustment not found'
                ]
            ], 404);
        }
    }
}
