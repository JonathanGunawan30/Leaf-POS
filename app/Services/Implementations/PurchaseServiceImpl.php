<?php

namespace App\Services\Implementations;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFound;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Tax;
use App\Services\Interfaces\PurchaseService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class PurchaseServiceImpl implements PurchaseService
{
    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $taxes = $data['taxes'] ?? [];
            $details = $data['purchase_details'] ?? [];
            $payments = $data['purchase_payments'] ?? [];

            unset($data['taxes'], $data['purchase_details'], $data['purchase_payments']);

            $totalAmount = 0;
            foreach ($details as &$detail) {
                $product = \App\Models\Product::findOrFail($detail['product_id']);
                $detail['unit_price'] = $product->purchase_price;
                $detail['sub_total'] = $detail['quantity'] * $detail['unit_price'];
                $totalAmount += $detail['sub_total'];
            }
            unset($detail);

            $totalTax = 0;
            foreach ($taxes as $tax) {
                $taxModel = \App\Models\Tax::find($tax['tax_id']);
                if ($taxModel) {
                    $totalTax += ($taxModel->rate / 100) * $totalAmount;
                }
            }

            $grandTotal = $totalAmount - $data['total_discount'] + $totalTax + $data['shipping_amount'];

            $data['total_amount'] = $totalAmount;
            $data['total_tax'] = $totalTax;
            $data['grand_total'] = $grandTotal;

            $data['user_id'] = Auth::user()->id;

            $purchase = \App\Models\Purchase::create($data);

            foreach ($details as $detail) {
                $purchase->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'sub_total' => $detail['sub_total'],
                    'note' => $detail['note'] ?? null,
                ]);

                if (isset($data['status']) && $data['status'] === 'delivered') {
                    $product = \App\Models\Product::find($detail['product_id']);
                    if ($product) {
                        $previousStock = $product->stock;
                        $product->stock += $detail['quantity'];
                        $product->save();


                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Purchase::class,
                            'reference_id' => $purchase->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => $detail['quantity'],
                            'movement_type' => 'purchase',
                            'notes' => 'Stock updated from purchase delivery',
                        ]);
                    }
                }
            }

            $totalPaid = 0;
            foreach ($payments as $payment) {
                $purchase->payments()->create([
                    'payment_date' => $payment['payment_date'],
                    'amount' => $payment['amount'],
                    'due_date' => $payment['due_date'] ?? null,
                    'payment_method' => $payment['payment_method'],
                    'status' => $payment['status'],
                    'note' => $payment['note'] ?? null,
                ]);
                $totalPaid += $payment['amount'];
            }

            if ($totalPaid >= $purchase->grand_total) {
                $purchase->payment_status = 'paid';
            } elseif ($totalPaid > 0) {
                $purchase->payment_status = 'partially_paid';
            } else {
                $purchase->payment_status = 'unpaid';
            }

            $purchase->save();

            foreach ($taxes as $tax) {
                $taxModel = \App\Models\Tax::find($tax['tax_id']);
                if ($taxModel) {
                    $amount = ($taxModel->rate / 100) * $totalAmount;
                    $purchase->taxes()->attach($taxModel->id, ['amount' => $amount]);
                }
            }

            return $purchase;
        });
    }




    public function show($id): Purchase
    {
        return Purchase::with(['user.role', 'details.product.unit', 'payments', 'taxes', 'supplier'])->findOrFail($id);
    }

    public function update(array $data, $id): Purchase
    {
        return DB::transaction(function () use ($data, $id) {
            $purchase = \App\Models\Purchase::with('details')->findOrFail($id);

            $oldStatus = $purchase->status;
            $oldDetails = $purchase->details->keyBy('product_id'); // Key by product_id for easier lookup

            $taxes = $data['taxes'] ?? null;
            $details = $data['purchase_details'] ?? null;
            $payments = $data['purchase_payments'] ?? null;

            unset($data['taxes'], $data['purchase_details'], $data['purchase_payments']);

            if ($details !== null) {
                $totalAmount = collect($details)->sum(function ($detail) {
                    $product = \App\Models\Product::find($detail['product_id']);
                    if (!$product) {
                        throw new ProductNotFound();
                    }
                    return $detail['quantity'] * $product->purchase_price;
                });

                if (!isset($data['total_amount'])) {
                    $data['total_amount'] = $totalAmount;
                }
            } else {
                $totalAmount = $purchase->total_amount;
            }

            $totalDiscount = $data['total_discount'] ?? $purchase->total_discount;
            $shippingAmount = $data['shipping_amount'] ?? $purchase->shipping_amount;

            $totalTax = 0;
            if ($taxes !== null) {
                $purchase->taxes()->detach();
                foreach ($taxes as $tax) {
                    $taxModel = \App\Models\Tax::find($tax['tax_id']);
                    if ($taxModel) {
                        $amount = ($taxModel->rate / 100) * $totalAmount;
                        $purchase->taxes()->attach($taxModel->id, ['amount' => $amount]);
                        $totalTax += $amount;
                    }
                }
            } else {
                $totalTax = $purchase->total_tax;
            }

            $data['grand_total'] = $totalAmount - $totalDiscount + $totalTax + $shippingAmount;
            $data['total_tax'] = $totalTax;

            $purchase->update($data);

            $detailChanges = [
                'added' => [],
                'updated' => [],
                'removed' => []
            ];

            if ($details !== null) {
                // First, process removals
                $currentProductIds = collect($details)->pluck('product_id')->toArray();
                foreach ($oldDetails as $oldDetail) {
                    if (!in_array($oldDetail->product_id, $currentProductIds)) {
                        $detailChanges['removed'][] = [
                            'product_id' => $oldDetail->product_id,
                            'quantity' => $oldDetail->quantity
                        ];
                        // Also delete the detail from the database
                        $purchase->details()->where('id', $oldDetail->id)->forceDelete();
                    }
                }

                foreach ($details as $detail) {
                    $product = \App\Models\Product::find($detail['product_id']);
                    if (!$product) {
                        throw new ProductNotFound();
                    }

                    $unitPrice = $product->purchase_price;
                    $subTotal = $detail['quantity'] * $unitPrice;

                    $oldDetail = $oldDetails->get($detail['product_id']);

                    if ($oldDetail) {
                        // Update existing detail
                        $quantityDiff = $detail['quantity'] - $oldDetail->quantity;
                        if ($quantityDiff != 0) {
                            $detailChanges['updated'][] = [
                                'product_id' => $detail['product_id'],
                                'quantity_diff' => $quantityDiff,
                                'old_quantity' => $oldDetail->quantity, // Add old_quantity for logging
                                'new_quantity' => $detail['quantity'] // Add new_quantity for logging
                            ];
                        }
                        $purchase->details()->where('id', $oldDetail->id)->update([
                            'quantity' => $detail['quantity'],
                            'unit_price' => $unitPrice,
                            'sub_total' => $subTotal,
                            'note' => $detail['note'] ?? null,
                        ]);
                    } else {
                        // Add new detail
                        $detailChanges['added'][] = [
                            'product_id' => $detail['product_id'],
                            'quantity' => $detail['quantity']
                        ];
                        $purchase->details()->create([
                            'product_id' => $detail['product_id'],
                            'quantity' => $detail['quantity'],
                            'unit_price' => $unitPrice,
                            'sub_total' => $subTotal,
                            'note' => $detail['note'] ?? null,
                        ]);
                    }
                }

                if (!isset($data['total_amount'])) {
                    $totalAmount = $purchase->details()->sum('sub_total');
                    $purchase->total_amount = $totalAmount;
                    $purchase->save();
                }
            }

            if ($payments !== null) {
                $keepPaymentIds = collect($payments)
                    ->filter(fn($payment) => isset($payment['id']))
                    ->pluck('id')
                    ->toArray();

                $purchase->payments()->whereNotIn('id', $keepPaymentIds)->forceDelete();

                foreach ($payments as $payment) {
                    if (isset($payment['id'])) {
                        $purchase->payments()->where('id', $payment['id'])->update([
                            'payment_date' => $payment['payment_date'],
                            'amount' => $payment['amount'],
                            'due_date' => $payment['due_date'] ?? null,
                            'payment_method' => $payment['payment_method'],
                            'status' => $payment['status'],
                            'note' => $payment['note'] ?? null,
                        ]);
                    } else {
                        $purchase->payments()->create([
                            'payment_date' => $payment['payment_date'],
                            'amount' => $payment['amount'],
                            'due_date' => $payment['due_date'] ?? null,
                            'payment_method' => $payment['payment_method'],
                            'status' => $payment['status'],
                            'note' => $payment['note'] ?? null,
                        ]);
                    }
                }

                $totalPaid = $purchase->payments()->where('status', 'paid')->sum('amount');

                if ($totalPaid >= $purchase->grand_total) {
                    $purchase->payment_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $purchase->payment_status = 'partially_paid';
                } else {
                    $purchase->payment_status = 'unpaid';
                }

                $purchase->save();
            }

            $newStatus = $purchase->status;

            if ($oldStatus !== 'delivered' && $newStatus === 'delivered') {
                foreach ($purchase->details as $detail) {
                    $product = \App\Models\Product::find($detail->product_id);
                    if ($product) {
                        $previousStock = $product->stock;
                        $product->increment('stock', $detail->quantity);
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Purchase::class,
                            'reference_id' => $purchase->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->fresh()->stock,
                            'quantity' => $detail->quantity,
                            'movement_type' => 'purchase', // Changed to 'purchase'
                            'notes' => 'Stock increased due to purchase delivery',
                        ]);
                    }
                }
            }

            elseif ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
                foreach ($purchase->details as $detail) {
                    $product = \App\Models\Product::find($detail->product_id);
                    if ($product) {
                        $previousStock = $product->stock;
                        if ($product->stock < $detail->quantity) {
                            throw new InsufficientStockException($product->name);
                        }
                        $product->decrement('stock', $detail->quantity);
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Purchase::class,
                            'reference_id' => $purchase->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->fresh()->stock,
                            'quantity' => -$detail->quantity,
                            'movement_type' => 'purchase', // Changed to 'purchase'
                            'notes' => 'Stock decreased due to purchase status change from delivered',
                        ]);
                    }
                }
            }

            elseif ($oldStatus === 'delivered' && $newStatus === 'delivered') {

                // Log added details
                foreach ($detailChanges['added'] as $addedDetail) {
                    $product = \App\Models\Product::find($addedDetail['product_id']);
                    if ($product) {
                        $previousStock = $product->stock;
                        $product->increment('stock', $addedDetail['quantity']);
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Purchase::class,
                            'reference_id' => $purchase->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->fresh()->stock,
                            'quantity' => $addedDetail['quantity'],
                            'movement_type' => 'purchase',
                            'notes' => 'Stock increased due to product added in delivered purchase',
                        ]);
                    }
                }

                // Log removed details
                foreach ($detailChanges['removed'] as $removedDetail) {
                    $product = \App\Models\Product::find($removedDetail['product_id']);
                    if ($product) {
                        $previousStock = $product->stock;
                        if ($product->stock < $removedDetail['quantity']) {
                            throw new InsufficientStockException($product->name);
                        }
                        $product->decrement('stock', $removedDetail['quantity']);
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Purchase::class,
                            'reference_id' => $purchase->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->fresh()->stock,
                            'quantity' => -$removedDetail['quantity'],
                            'movement_type' => 'purchase',
                            'notes' => 'Stock decreased due to product removed from delivered purchase',
                        ]);
                    }
                }

                // Log updated details
                foreach ($detailChanges['updated'] as $updatedDetail) {
                    if ($updatedDetail['quantity_diff'] != 0) {
                        $product = \App\Models\Product::find($updatedDetail['product_id']);
                        if ($product) {
                            $previousStock = $product->stock;
                            if ($updatedDetail['quantity_diff'] > 0) {
                                $product->increment('stock', $updatedDetail['quantity_diff']);
                                \App\Models\StockMovement::create([
                                    'product_id' => $product->id,
                                    'user_id' => auth()->id(),
                                    'reference_type' => \App\Models\Purchase::class,
                                    'reference_id' => $purchase->id,
                                    'previous_stock' => $previousStock,
                                    'new_stock' => $product->fresh()->stock,
                                    'quantity' => $updatedDetail['quantity_diff'],
                                    'movement_type' => 'purchase',
                                    'notes' => "Stock increased due to quantity update in delivered purchase. Old quantity: {$updatedDetail['old_quantity']}, New quantity: {$updatedDetail['new_quantity']}",
                                ]);
                            } else {
                                $quantityToReduce = abs($updatedDetail['quantity_diff']);
                                if ($product->stock < $quantityToReduce) {
                                    throw new InsufficientStockException($product->name);
                                }
                                $product->decrement('stock', $quantityToReduce);
                                \App\Models\StockMovement::create([
                                    'product_id' => $product->id,
                                    'user_id' => auth()->id(),
                                    'reference_type' => \App\Models\Purchase::class,
                                    'reference_id' => $purchase->id,
                                    'previous_stock' => $previousStock,
                                    'new_stock' => $product->fresh()->stock,
                                    'quantity' => -$quantityToReduce,
                                    'movement_type' => 'purchase',
                                    'notes' => "Stock decreased due to quantity update in delivered purchase. Old quantity: {$updatedDetail['old_quantity']}, New quantity: {$updatedDetail['new_quantity']}",
                                ]);
                            }
                        }
                    }
                }

            }

            return $purchase->fresh()->load(['details.product', 'payments', 'taxes', 'supplier']);
        });
    }

    public function getAll(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Purchase::with(['user.role', 'details.product.unit', 'payments', 'taxes', 'supplier'])->latest();

        if ($search = request()->get('search')) {
            $query->where(function($query) use($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhere('grand_total', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })

                    ->orWhereHas('details.product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })

                    ->orWhereHas('details', function ($q) use ($search) {
                        $q->where('note', 'like', "%{$search}%");
                    })
                    ->orWhereHas('payments', function ($q) use ($search) {
                        $q->where('payment_method', 'like', "%{$search}%")
                            ->orWhere('note', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($invoice = request()->get('invoice')) {
            $query->where('invoice_number', 'like', "%{$invoice}%");
        }

        if($status = request()->get('status')){
            $query->where('status', $status);
        }

        if($paymentStatus = request()->get('payment_status')){
            $query->where('payment_status', $paymentStatus);
        }

        if($supplierName = request()->get('supplier_name')){
            $query->whereHas('supplier', function($query) use($supplierName){
                $query->where('name', 'like', "%{$supplierName}%");
            });
        }

        if($product_name = request()->get('product_name')){
            $query->whereHas('details.product', function($query) use($product_name){
                $query->where('name', 'like', "%{$product_name}%");
            });
        }

        if($startDate = request()->get('start_date')){
            $query->whereDate('purchase_date', '>=', $startDate);
        }

        if($endDate = request()->get('end_date')){
            $query->whereDate('purchase_date', '<=', $endDate);
        }

        if($dueDateStart = request()->get('start_due_date')){
            $query->whereDate('due_date', '>=', $dueDateStart);
        }

        if($dueDateEnd = request()->get('end_due_date')){
            $query->whereDate('due_date', '<=', $dueDateEnd);
        }

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function softdelete($id): Purchase
    {
        return DB::transaction(function () use ($id) {
            $purchase = Purchase::with('details.product')->findOrFail($id);

            if ($purchase->trashed()) {
                throw new \Exception("Purchase is already deleted", 400);
            }

            if ($purchase->status === 'delivered') {
                foreach ($purchase->details as $detail) {
                    $product = $detail->product;

                    if (!$product) {
                        throw new \Exception("Product not found for purchase detail ID {$detail->id}", 404);
                    }

                    if ($product->stock < $detail->quantity) {
                        throw new \Exception("Not enough stock to reverse purchase for product: {$product->name}", 400);
                    }

                    $product->stock -= $detail->quantity;
                    $product->save();
                }
            }

            $purchase->delete();

            return $purchase;
        });
    }

    public function restore($id): Purchase
    {
        return DB::transaction(function () use ($id) {
            $purchase = Purchase::withTrashed()
                ->with(['details' => function ($query) {
                    $query->withTrashed()->with('product');
                }, 'payments' => function ($query) {
                    $query->withTrashed();
                }])
                ->findOrFail($id);

            if (!$purchase->trashed()) {
                throw new \Exception("Cannot restore, purchase is not deleted", 400);
            }

            $purchase->restore();

            foreach ($purchase->details as $detail) {
                $detail->restore();

                if ($purchase->status === 'delivered') {
                    $product = $detail->product;

                    if (!$product) {
                        throw new \Exception("Product not found for purchase detail ID {$detail->id}", 404);
                    }

                    $previousStock = $product->stock;
                    $product->stock += $detail->quantity;
                    $product->save();

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'reference_type' => \App\Models\Purchase::class,
                        'reference_id' => $purchase->id,
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->stock,
                        'quantity' => $detail->quantity,
                        'movement_type' => 'purchase',
                        'notes' => 'Stock increased due to restoration of purchase #' . $purchase->id,
                    ]);
                }
            }

            foreach ($purchase->payments as $payment) {
                $payment->restore();
            }

            return $purchase;
        });
    }


    public function trashed(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Purchase::onlyTrashed()
            ->with([
                'details' => function ($query) {
                    $query->withTrashed();
                },
                'payments' => function ($query) {
                    $query->withTrashed();
                },
                'taxes', 'supplier', 'user.role'
            ]);

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function harddelete($id): Purchase
    {
        $purchase = Purchase::withTrashed()->with(['details', 'payments', 'taxes'])->findOrFail($id);

        if(!$purchase->trashed()){
            throw new \Exception("Cannot delete, purchase is not deleted", 400);
        }

        $purchase->details->each->forceDelete();
        $purchase->payments->each->forceDelete();
        $purchase->taxes()->detach();
        $purchase->forceDelete();
        return $purchase;
    }


}
