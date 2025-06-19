<?php

namespace App\Services\Implementations;

use App\Exceptions\ProductNotFoundInPurchaseException;
use App\Exceptions\ReturnedQuantityExceedsOriginalException;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\StockMovement;
use App\Models\Tax;
use App\Services\Interfaces\PurchaseReturnService;
use http\Env\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnServiceImpl implements PurchaseReturnService
{
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            $returnDetails = $data['purchase_return_details'] ?? [];
            $returnPayments = $data['purchase_return_payments'] ?? [];
            $returnTaxes = $data['purchase_return_taxes'] ?? [];

            $originalPurchase = Purchase::with('details.product', 'taxes')
                ->where('invoice_number', $data['invoice_number'])
                ->firstOrFail();

            $originalPurchaseDetails = $originalPurchase->details->keyBy('product_id');

            unset(
                $data['purchase_return_details'],
                $data['purchase_return_payments'],
                $data['purchase_return_taxes']
            );

            $data['purchase_id'] = $originalPurchase->id;

            $compensationMethod = $data['compensation_method'] ?? 'refund';

            $totalAmountReturn = 0;
            $totalDiscountReturn = 0;
            $validatedReturnDetails = [];

            foreach ($returnDetails as $detail) {
                $productId = $detail['product_id'];
                $returnedQuantity = $detail['quantity'];

                if (!isset($originalPurchaseDetails[$productId])) {
                    throw new ProductNotFoundInPurchaseException($productId);
                }

                $originalDetail = $originalPurchaseDetails[$productId];

                if ($returnedQuantity > $originalDetail->quantity) {
                    throw new ReturnedQuantityExceedsOriginalException(
                        $productId,
                        $returnedQuantity,
                        $originalDetail->quantity
                    );
                }

                $unitPrice = $originalDetail->unit_price;

                $product = Product::find($productId);
                $discountRate = $product?->discount ?? 0;

                $discountPerUnit = ($discountRate / 100) * $unitPrice;
                $totalDiscount = $discountPerUnit * $returnedQuantity;

                $subTotal = ($unitPrice * $returnedQuantity) - $totalDiscount;

                $totalAmountReturn += $subTotal;
                $totalDiscountReturn += $totalDiscount;

                $validatedReturnDetails[] = [
                    'product_id' => $productId,
                    'quantity' => $returnedQuantity,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                    'note' => $detail['note'] ?? null,
                ];
            }

            $totalTaxReturn = 0;
            $processedTaxesReturn = [];
            $grandTotalReturn = 0;

            if ($compensationMethod === 'refund') {
                foreach ($originalPurchase->taxes as $taxPivot) {
                    $taxModel = Tax::find($taxPivot->id);
                    if ($taxModel) {
                        $taxAmount = ($taxModel->rate / 100) * $totalAmountReturn;
                        $totalTaxReturn += $taxAmount;
                        $processedTaxesReturn[] = [
                            'tax_id' => $taxPivot->id,
                            'amount' => $taxAmount
                        ];
                    }
                }

                $totalDiscountReturn = $data['total_discount'] ?? 0;
                $originalTotalAmount = $originalPurchase->total_amount;
                $shippingAmountReturn = $originalPurchase->shipping_amount ?? 0;
                $grandTotalReturn = $totalAmountReturn - $totalDiscountReturn + $totalTaxReturn + $shippingAmountReturn;

                $data['shipping_amount'] = $shippingAmountReturn;


                $data['total_amount'] = $totalAmountReturn;
            } else {
                foreach ($originalPurchase->taxes as $taxPivot) {
                    $taxModel = Tax::find($taxPivot->id);
                    if ($taxModel) {
                        $taxAmount = ($taxModel->rate / 100) * $totalAmountReturn;
                        $totalTaxReturn += $taxAmount;
                        $processedTaxesReturn[] = [
                            'tax_id' => $taxPivot->id,
                            'amount' => $taxAmount
                        ];
                    }
                }

                $totalDiscountReturn = 0;
                $shippingAmountReturn = $originalPurchase->shipping_amount ?? 0;
                $grandTotalReturn = $shippingAmountReturn;

                $data['total_amount'] = $totalAmountReturn;
                $data['shipping_amount'] = $shippingAmountReturn;
            }

            $data['supplier_id'] = $originalPurchase->supplier_id;
            $data['total_tax'] = $totalTaxReturn;
            $data['total_discount'] = $totalDiscountReturn;
            $data['grand_total'] = $grandTotalReturn;
            $data['user_id'] = Auth::user()->id;
            $data['invoice_number_returns'] = $this->generatePurchaseReturnInvoiceNumber();
            $data['delivery_number_returns'] = $this->generatePurchaseReturnDeliveryNumber();


            $purchaseReturn = PurchaseReturn::create($data);

            foreach ($validatedReturnDetails as $detail) {
                $purchaseReturn->details()->create($detail);

                if (in_array($purchaseReturn->status, ['approved', 'completed'])) {
                    $product = Product::find($detail['product_id']);
                    if ($product) {
                        if ($compensationMethod === 'refund') {
                            $previousStock = $product->stock;
                            $product->stock -= $detail['quantity'];
                            $product->save();

                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'reference_type' => PurchaseReturn::class,
                                'reference_id' => $purchaseReturn->id,
                                'previous_stock' => $previousStock,
                                'new_stock' => $product->stock,
                                'quantity' => -$detail['quantity'],
                                'movement_type' => 'purchase_return',
                                'notes' => 'Stock decreased - returned defective items to supplier (refund)',
                            ]);

                            $originalPurchaseDetail = $originalPurchase->details()
                                ->where('product_id', $detail['product_id'])
                                ->first();

                            if ($originalPurchaseDetail) {
                                $originalPurchaseDetail->decrement('quantity', $detail['quantity']);
                                $originalPurchaseDetail->sub_total = $originalPurchaseDetail->quantity * $originalPurchaseDetail->unit_price;
                                $originalPurchaseDetail->save();
                            }

                        } elseif ($compensationMethod === 'replacement') {
                            $previousStock = $product->stock;
                            $product->stock -= $detail['quantity'];
                            $product->save();

                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'reference_type' => PurchaseReturn::class,
                                'reference_id' => $purchaseReturn->id,
                                'previous_stock' => $previousStock,
                                'new_stock' => $product->stock,
                                'quantity' => -$detail['quantity'],
                                'movement_type' => 'purchase_return',
                                'notes' => 'Stock decreased - returned defective items to supplier (replacement)',
                            ]);

                            $previousStock = $product->stock;
                            $product->stock += $detail['quantity'];
                            $product->save();

                            StockMovement::create([
                                'product_id' => $product->id,
                                'user_id' => auth()->id(),
                                'reference_type' => PurchaseReturn::class,
                                'reference_id' => $purchaseReturn->id,
                                'previous_stock' => $previousStock,
                                'new_stock' => $product->stock,
                                'quantity' => $detail['quantity'],
                                'movement_type' => 'purchase_return',
                                'notes' => 'Stock increased - received replacement items from supplier',
                            ]);
                        }
                    }
                }
            }

            if ($compensationMethod === 'refund') {
                $originalPurchase->refresh();
                $newTotalAmountOriginalPurchase = $originalPurchase->details->sum('sub_total');

                $newTotalTaxOriginalPurchase = 0;
                foreach ($originalPurchase->taxes as $taxPivot) {
                    $taxModel = Tax::find($taxPivot->id);
                    if ($taxModel) {
                        $taxAmount = ($taxModel->rate / 100) * $newTotalAmountOriginalPurchase;
                        $newTotalTaxOriginalPurchase += $taxAmount;

                        $originalPurchase->taxes()->updateExistingPivot($taxModel->id, [
                            'amount' => $taxAmount
                        ]);
                    }
                }

                $originalDiscount = $originalPurchase->total_discount;
                $originalShippingAmount = $originalPurchase->shipping_amount;
                $newGrandTotalOriginalPurchase = $newTotalAmountOriginalPurchase - $originalDiscount + $newTotalTaxOriginalPurchase + $originalShippingAmount;

                $originalPurchase->update([
                    'total_amount' => $newTotalAmountOriginalPurchase,
                    'total_tax' => $newTotalTaxOriginalPurchase,
                    'grand_total' => $newGrandTotalOriginalPurchase,
                ]);
            }

            $totalRefunded = 0;
            foreach ($returnPayments as $payment) {
                $purchaseReturn->payments()->create([
                    'payment_date' => $payment['payment_date'],
                    'amount' => $payment['amount'],
                    'due_date' => $payment['due_date'] ?? null,
                    'payment_method' => $payment['payment_method'],
                    'status' => $payment['status'],
                    'note' => $payment['note'] ?? null,
                ]);
                $totalRefunded += $payment['amount'];
            }

            if ($compensationMethod === 'refund') {
                if ($totalRefunded >= $purchaseReturn->grand_total) {
                    $purchaseReturn->payment_status = 'paid';
                } elseif ($totalRefunded > 0) {
                    $purchaseReturn->payment_status = 'partially_paid';
                } else {
                    $purchaseReturn->payment_status = 'unpaid';
                }
            } elseif ($compensationMethod === 'replacement') {
                $purchaseReturn->payment_status = 'paid';
            }

            $purchaseReturn->save();

            foreach ($processedTaxesReturn as $tax) {
                $purchaseReturn->taxes()->attach($tax['tax_id'], ['amount' => $tax['amount']]);
            }

            return $purchaseReturn;
        });
    }

    private function generatePurchaseReturnInvoiceNumber(): string
    {
        $now = now();

        $tanggal = $now->format('Ymd');
        $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$now->month - 1];
        $tahunRomawi = $this->toRoman((int) $now->format('y'));

        do {
            $randomNumber = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $invoiceNumber = "PRT/{$tanggal}/{$tahunRomawi}/{$bulanRomawi}/{$randomNumber}";
        } while (PurchaseReturn::where('invoice_number_returns', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
    private function generatePurchaseReturnDeliveryNumber(): string
    {
        $now = now();

        $tanggal = $now->format('Ymd');
        $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$now->month - 1];
        $tahunRomawi = $this->toRoman((int) $now->format('y'));

        do {
            $randomNumber = str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $deliveryNumber = "SJR/{$tanggal}/{$tahunRomawi}/{$bulanRomawi}/{$randomNumber}";
        } while (PurchaseReturn::where('delivery_number_returns', $deliveryNumber)->exists());

        return $deliveryNumber;
    }
    private function toRoman(int $number): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function getAll()
    {
        $request = request();

        $query = PurchaseReturn::with(['user', 'details.product', 'payments', 'taxes']);

        if ($search = request()->get('search')) {
            $query->where(function($query) use($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('invoice_number_returns', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }


        if ($invoice = $request->get('invoice')) {
            $query->where('invoice_number', 'like', "%{$invoice}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($method = $request->get('method')){
            $query->where('compensation_method', $method);
        }

        if ($supplierName = $request->get('supplier_name')) {
            $query->whereHas('purchase.supplier', function(Builder $supplierQuery) use($supplierName){
                $supplierQuery->where('name', 'like', "%{$supplierName}%")
                    ->orWhere('company_name', 'like', "%{$supplierName}%");
            });
        }

        if ($product_name = $request->get('product_name')) {
            $query->whereHas('purchaseReturnDetails.product', function(Builder $productQuery) use($product_name){
                $productQuery->where('name', 'like', "%{$product_name}%");
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('return_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->whereDate('return_date', '<=', $endDate);
        }

        $perPage = $request->get('per_page', 10);
        $query->orderBy('return_date', 'desc');
        return $query->paginate($perPage);
    }

    public function find($id)
    {
        return PurchaseReturn::withTrashed()
            ->with([
                'user.role',
                'details.product.unit',
                'details.product.category',
                'details.product.taxes',
                'payments',
                'taxes',
                'purchase',
                'supplier'
            ])
            ->findOrFail($id);
    }


    public function update($validatedData, $id)
    {
        return DB::transaction(function () use ($validatedData, $id) {
            $purchaseReturn = PurchaseReturn::with([
                'user', 'details.product.unit', 'details.product.category', 'details.product.taxes',
                'payments', 'taxes', 'purchase', 'user.role', 'supplier',
            ])->findOrFail($id);

            $purchaseReturn->update([
                'invoice_number' => $validatedData['invoice_number'],
                'return_date' => $validatedData['return_date'],
                'status' => $validatedData['status'],
                'payment_status' => $validatedData['payment_status'],
                'compensation_method' => $validatedData['compensation_method'],
                'reason' => $validatedData['reason'],
            ]);

            if (!empty($validatedData['purchase_return_details'])) {
                $details = collect($validatedData['purchase_return_details'])->map(function ($detail) {
                    return [
                        'product_id' => $detail['product_id'],
                        'quantity' => $detail['quantity'],
                        'unit_price' => $detail['unit_price'] ?? 0,
                        'sub_total' => $detail['sub_total'] ?? 0,
                        'note' => $detail['note'] ?? null,
                    ];
                });

                $purchaseReturn->details()->delete(); // nama relasi: details
                $purchaseReturn->details()->createMany($details->toArray());
            }

            if (!empty($validatedData['purchase_return_payments'])) {
                $payments = collect($validatedData['purchase_return_payments'])->map(function ($payment) {
                    return [
                        'payment_date' => $payment['payment_date'],
                        'amount' => $payment['amount'],
                        'due_date' => $payment['due_date'] ?? null,
                        'payment_method' => $payment['payment_method'],
                        'status' => $payment['status'],
                        'note' => $payment['note'] ?? null,
                    ];
                });

                $purchaseReturn->payments()->delete(); // nama relasi: payments
                $purchaseReturn->payments()->createMany($payments->toArray());
            }

            $purchaseReturn->load(['details', 'payments', 'taxes']);

            $taxesToSync = [];
            if (!empty($validatedData['purchase_return_taxes'])) {
                $taxIds = collect($validatedData['purchase_return_taxes'])->pluck('tax_id')->unique()->toArray();
                $masterTaxes = Tax::whereIn('id', $taxIds)->get()->keyBy('id');

                $subTotal = $purchaseReturn->details->sum('sub_total');

                foreach ($validatedData['purchase_return_taxes'] as $taxData) {
                    $taxId = $taxData['tax_id'];
                    $rate = $masterTaxes[$taxId]->rate ?? 0;
                    $amount = round($subTotal * ($rate / 100), 2);
                    $taxesToSync[$taxId] = ['amount' => $amount];
                }

                $purchaseReturn->taxes()->sync($taxesToSync);
            }

            $purchaseReturn->load(['details', 'payments', 'taxes']);

            $totalAmount = $purchaseReturn->details->sum('sub_total');
            $totalTax = $purchaseReturn->taxes->sum('pivot.amount');
            $totalDiscount = $validatedData['total_discount'] ?? 0;
            $shippingAmount = $purchaseReturn->purchase->shipping_amount ?? 0;

            if ($purchaseReturn->compensation_method === 'replacement') {
                $grandTotal = $shippingAmount;
            } else {
                $grandTotal = $totalAmount - $totalDiscount + $totalTax + $shippingAmount;
            }


            $purchaseReturn->update([
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'total_discount' => $totalDiscount,
                'shipping_amount' => $shippingAmount,
            ]);

            $refundedAmount = $purchaseReturn->payments->sum('amount');
            $purchaseReturn->payment_status = match (true) {
                $refundedAmount >= $grandTotal => 'paid',
                $refundedAmount > 0 => 'partially_paid',
                default => 'unpaid',
            };
            $purchaseReturn->save();

            return $purchaseReturn->fresh([
                'user', 'details.product.unit', 'details.product.category', 'details.product.taxes',
                'payments', 'taxes', 'purchase', 'user.role', 'supplier',
            ]);
        });
    }

    public function recalculatePurchaseTotals(Purchase $purchase): void
    {
        $purchase->refresh();
        $purchase->load('details', 'taxes');
        $newTotalAmount = $purchase->details->sum('sub_total');
        $newTotalTax = 0;

        foreach ($purchase->taxes as $taxPivot) {
            $taxModel = Tax::find($taxPivot->id);
            if ($taxModel) {
                $taxAmount = ($taxModel->rate / 100) * $newTotalAmount;
                $newTotalTax += $taxAmount;

                $purchase->taxes()->updateExistingPivot($taxModel->id, [
                    'amount' => $taxAmount,
                ]);
            }
        }

        $purchase->update([
            'total_amount' => $newTotalAmount,
            'total_tax' => $newTotalTax,
            'grand_total' => $newTotalAmount - $purchase->total_discount + $newTotalTax + $purchase->shipping_amount,
        ]);
    }


    public function softDelete($id): PurchaseReturn
    {
        return DB::transaction(function () use ($id) {
            $purchaseReturn = PurchaseReturn::with(['details.product', 'purchase'])
                ->findOrFail($id);

            if (!$purchaseReturn && $purchaseReturn::withTrashed()->where('id', $id)->exists()) {
                throw new \Exception("Purchase Return is already deleted", 400);
            }

            if (in_array($purchaseReturn->status, ['approved', 'completed'])) {
                foreach ($purchaseReturn->details as $detail) {
                    $product = $detail->product;

                    if (!$product) {
                        throw new \Exception("Product not found for return detail ID {$detail->id}", 404);
                    }

                    $previousStock = $product->stock;

                    if ($purchaseReturn->compensation_method === 'refund') {
                        // Barang sudah dikeluarkan, maka harus dikembalikan
                        $product->stock += $detail->quantity;
                        $product->save();

                        $originalPurchaseDetail = $purchaseReturn->purchase->details()
                            ->where('product_id', $detail->product_id)
                            ->first();

                        if ($originalPurchaseDetail) {
                            $originalPurchaseDetail->increment('quantity', $detail->quantity);

                            $originalPurchaseDetail->refresh();
                            $originalPurchaseDetail->sub_total = $originalPurchaseDetail->quantity * $originalPurchaseDetail->unit_price;
                            $originalPurchaseDetail->save();

                            $this->recalculatePurchaseTotals($purchaseReturn->purchase->fresh());
                        }


                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => $detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Rollback refund: stock returned because purchase return was soft-deleted.',
                        ]);

                    } elseif ($purchaseReturn->compensation_method === 'replacement') {
                        // Barang dikembalikan lalu diganti → rollback dua pergerakan
                        if ($product->stock < $detail->quantity) {
                            throw new \Exception("Not enough stock to reverse replacement for product: {$product->name}", 400);
                        }

                        // 1. Kurangi pengganti (replacement_in)
                        $product->stock -= $detail->quantity;
                        $product->save();

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => -$detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Rollback replacement in: stock reduced due to soft delete.',
                        ]);

                        // 2. Tambahkan kembali barang yang sebelumnya dikembalikan
                        $previousStock = $product->stock;
                        $product->stock += $detail->quantity;
                        $product->save();

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => $detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Rollback replacement out: stock returned because purchase return was soft-deleted.',
                        ]);
                    }
                }
            }

            $purchaseReturn->delete();

            return $purchaseReturn;
        });
    }


    public function restore($id): PurchaseReturn
    {
        return DB::transaction(function () use ($id) {
            $purchaseReturn = PurchaseReturn::withTrashed()
                ->with(['details' => function ($query) {
                    $query->withTrashed()->with('product');
                }, 'purchase'])
                ->findOrFail($id);

            if (!$purchaseReturn->trashed()) {
                throw new \Exception("Cannot restore, purchase return is not deleted", 400);
            }

            $purchaseReturn->restore();

            foreach ($purchaseReturn->details as $detail) {
                $detail->restore();

                $product = $detail->product;
                if (!$product) {
                    throw new \Exception("Product not found for return detail ID {$detail->id}", 404);
                }

                if (in_array($purchaseReturn->status, ['approved', 'completed'])) {
                    $previousStock = $product->stock;

                    if ($purchaseReturn->compensation_method === 'refund') {
                        if ($product->stock < $detail->quantity) {
                            throw new \Exception("Not enough stock to restore refund for product: {$product->name}", 400);
                        }

                        $product->stock -= $detail->quantity;
                        $product->save();

                        $originalPurchaseDetail = $purchaseReturn->purchase->details()
                            ->where('product_id', $detail->product_id)
                            ->first();

                        if ($originalPurchaseDetail) {
                            if ($originalPurchaseDetail->quantity < $detail->quantity) {
                                throw new \Exception("Cannot restore: purchase detail quantity insufficient for product: {$product->name}", 400);
                            }

                            $originalPurchaseDetail->decrement('quantity', $detail->quantity);

                            $originalPurchaseDetail->refresh();
                            $originalPurchaseDetail->sub_total = $originalPurchaseDetail->quantity * $originalPurchaseDetail->unit_price;
                            $originalPurchaseDetail->save();
                        }

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => -$detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Stock reduced due to restoration of purchase return (refund).',
                        ]);
                    }

                    if ($purchaseReturn->compensation_method === 'replacement') {
                        $product->stock += $detail->quantity;
                        $product->save();

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => $detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Restored replacement in: stock added back.',
                        ]);

                        $previousStock = $product->stock;

                        if ($product->stock < $detail->quantity) {
                            throw new \Exception("Not enough stock to restore replacement out for product: {$product->name}", 400);
                        }

                        $product->stock -= $detail->quantity;
                        $product->save();

                        StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => PurchaseReturn::class,
                            'reference_id' => $purchaseReturn->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => -$detail->quantity,
                            'movement_type' => 'purchase_return',
                            'notes' => 'Restored replacement out: stock deducted again.',
                        ]);
                    }
                }
            }

            if ($purchaseReturn->compensation_method === 'refund' &&
                in_array($purchaseReturn->status, ['approved', 'completed'])) {
                $this->recalculatePurchaseTotals($purchaseReturn->purchase->fresh());
            }

            return $purchaseReturn;
        });
    }


    public function harddelete($id): PurchaseReturn
    {
        $return = PurchaseReturn::withTrashed()->with(['details', 'payments', 'taxes'])->findOrFail($id);

        if (!$return::onlyTrashed()->where('id', $id)->exists()) {
            throw new \Exception("Cannot delete, return is not deleted", 400);
        }

        $return->details->each->forceDelete();
        $return->payments->each->forceDelete();
        $return->taxes()->detach();

        $return->forceDelete();

        return $return;
    }


    public function trashed(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = PurchaseReturn::onlyTrashed()
            ->with([
                'details' => function ($query) {
                    $query->withTrashed()->with('product');
                },
                'payments' => function ($query) {
                    $query->withTrashed();
                },
                'taxes', 'supplier', 'user.role', 'purchase'
            ]);

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

}
