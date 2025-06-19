<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the stock movements.
     */
    public function index(Request $request)
    {
        try {
            $query = StockMovement::with(['product', 'user'])
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($query) use ($search) {
                        $query->whereHas('product', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                        })
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhere('movement_type', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('product_id'), function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                })
                ->when($request->filled('movement_type'), function ($q) use ($request) {
                    $q->where('movement_type', $request->movement_type);
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
            $stockMovements = $query->paginate($perPage);

            return response()->json([
                'data' => $stockMovements,
                'message' => 'Stock movements retrieved successfully',
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
     * Display the specified stock movement.
     */
    public function show($id)
    {
        try {
            $stockMovement = StockMovement::with(['product', 'user'])->findOrFail($id);

            return response()->json([
                'data' => $stockMovement,
                'message' => 'Stock movement retrieved successfully',
                'statusCode' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'message' => 'Stock movement not found'
                ]
            ], 404);
        }
    }

    /**
     * Create a stock movement record.
     * This method is typically called internally by other controllers.
     */
    public function createMovement($product, $user, $reference, $previousStock, $newStock, $movementType, $notes = null)
    {
        $quantity = $newStock - $previousStock;

        return StockMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'reference_type' => get_class($reference),
            'reference_id' => $reference->id,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'quantity' => $quantity,
            'movement_type' => $movementType,
            'notes' => $notes
        ]);
    }
}
