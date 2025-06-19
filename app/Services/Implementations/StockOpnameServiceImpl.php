<?php

namespace App\Services\Implementations;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Services\Interfaces\StockOpnameService;
use Exception;
use Illuminate\Support\Facades\DB;

class StockOpnameServiceImpl implements StockOpnameService
{
    public function store($data)
    {

            return DB::transaction(function () use ($data) {
                $stockOpname = StockOpname::create([
                    'user_id' => auth()->id(),
                    'opname_date' => $data['opname_date'] ?? now(),
                    'status' => $data['status'] ?? 'draft',
                    'notes' => $data['notes'] ?? null,
                    'location' => $data['location'] ?? null,
                    'approved_by' => $data['approved_by'] ?? null,
                ]);

                $itemsData = collect($data['items'])->map(function ($itemData) use ($stockOpname) {
                    $systemStock = $this->getSystemStock($itemData['product_id']);
                    $actualStock = $itemData['actual_stock'];

                    return [
                        'stock_opname_id' => $stockOpname->id,
                        'product_id' => $itemData['product_id'],
                        'system_stock' => $systemStock,
                        'actual_stock' => $actualStock,
                        'difference' => $actualStock - $systemStock,
                        'notes' => $itemData['notes'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

                StockOpnameItem::insert($itemsData);

                return $stockOpname->load('items.product', 'user');
            });

    }

    private function getSystemStock($productId)
    {
        $product = Product::find($productId);
        return $product ? $product->stock : 0;
    }

    public function getAll($request)
    {
        $query = StockOpname::with(['user', 'items.product'])
        ->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where(function ($query) use ($search) {
                $query->where('notes', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('location'), function ($q) use ($request) {
                $q->where('location', 'like', "%{$request->location}%");
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->where('opname_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->where('opname_date', '<=', $request->end_date);
            });

        $sortField = $request->sort_field ?? 'opname_date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 10;
        return $query->paginate($perPage);
    }

    public function show($id)
    {
        return StockOpname::with(['user', 'items.product'])->findOrFail($id);
    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $stockOpname = StockOpname::findOrFail($id);

            $stockOpname->update([
                'opname_date' => $data['opname_date'] ?? $stockOpname->opname_date,
                'status' => $data['status'] ?? $stockOpname->status,
                'notes' => $data['notes'] ?? $stockOpname->notes,
                'location' => $data['location'] ?? $stockOpname->location,
                'approved_by' => $data['approved_by'] ?? $stockOpname->approved_by,
            ]);

            if (isset($data['items'])) {
                $this->updateStockOpnameItems($stockOpname, $data['items']);
            }

            return $stockOpname->load('items.product', 'user');
        });
    }

    protected function updateStockOpnameItems($stockOpname, $itemsData)
    {
        $existingItemIds = $stockOpname->items->pluck('id')->toArray();
        $updatedItemIds = [];

        foreach ($itemsData as $itemData) {
            $systemStock = $this->getSystemStock($itemData['product_id']);
            $actualStock = $itemData['actual_stock'];

            $itemData = [
                'product_id' => $itemData['product_id'],
                'system_stock' => $systemStock,
                'actual_stock' => $actualStock,
                'difference' => $actualStock - $systemStock,
                'notes' => $itemData['notes'] ?? null,
                'updated_at' => now(),
            ];

            if (isset($itemData['id'])) {
                StockOpnameItem::where('id', $itemData['id'])
                    ->where('stock_opname_id', $stockOpname->id)
                    ->update($itemData);
                $updatedItemIds[] = $itemData['id'];
            } else {
                $itemData['stock_opname_id'] = $stockOpname->id;
                $itemData['created_at'] = now();
                $newItem = StockOpnameItem::create($itemData);
                $updatedItemIds[] = $newItem->id;
            }
        }
        $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            StockOpnameItem::whereIn('id', $itemsToDelete)->delete();
        }
    }

    public function delete($id)
    {
        $stockOpname = StockOpname::findOrFail($id);
        $stockOpname->delete();
    }

    public function forceDelete($id)
    {
        $stockOpname = StockOpname::withTrashed()->findOrFail($id);
        DB::transaction(function () use ($stockOpname) {
            StockOpnameItem::where('stock_opname_id', $stockOpname->id)->delete();
            $stockOpname->forceDelete();
        });
    }

    public function restore($id)
    {
        $stockOpname = StockOpname::onlyTrashed()->findOrFail($id);
        $stockOpname->restore();
    }

    public function trashed()
    {
        $request = request();
        $query = StockOpname::onlyTrashed()
            ->with(['user', 'items' => function($query) {
                $query->withTrashed()->with('product');
            }])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('notes', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('location'), function ($q) use ($request) {
                $q->where('location', 'like', "%{$request->location}%");
            })
            ->when($request->filled('start_date'), function ($q) use ($request) {
                $q->where('opname_date', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($q) use ($request) {
                $q->where('opname_date', '<=', $request->end_date);
            });

        $sortField = $request->sort_field ?? 'deleted_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 10;
        return $query->paginate($perPage);
    }


}
