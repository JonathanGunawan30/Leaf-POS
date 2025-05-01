<?php

namespace App\Services\Implementations;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFound;
use App\Models\Purchase;
use App\Models\Tax;
use App\Services\Interfaces\PurchaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseServiceImpl implements PurchaseService
{
    public function store($data)
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
                        $product->stock += $detail['quantity'];
                        $product->save();
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




    public function show($id)
    {
        return Purchase::with(['user.role', 'details.product.unit', 'payments', 'taxes', 'supplier'])->findOrFail($id);

    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $purchase = \App\Models\Purchase::with('details')->findOrFail($id);

            $oldStatus = $purchase->status; // simpan status lama

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

            if ($details !== null) {
                $keepDetailIds = collect($details)
                    ->filter(fn($detail) => isset($detail['id']))
                    ->pluck('id')
                    ->toArray();

                $purchase->details()->whereNotIn('id', $keepDetailIds)->delete();

                foreach ($details as $detail) {
                    $product = \App\Models\Product::find($detail['product_id']);
                    if (!$product) {
                       throw new ProductNotFound();
                    }

                    $unitPrice = $product->purchase_price;
                    $subTotal = $detail['quantity'] * $unitPrice;

                    if (isset($detail['id'])) {
                        $purchase->details()->where('id', $detail['id'])->update([
                            'product_id' => $detail['product_id'],
                            'quantity' => $detail['quantity'],
                            'unit_price' => $unitPrice,
                            'sub_total' => $subTotal,
                            'note' => $detail['note'] ?? null,
                        ]);
                    } else {
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

                $purchase->payments()->whereNotIn('id', $keepPaymentIds)->delete();

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

            // LOGIC TAMBAH/KURANGI STOCK BERDASARKAN PERUBAHAN STATUS
            $newStatus = $purchase->status;

            if ($oldStatus !== 'delivered' && $newStatus === 'delivered') {
                // Status berubah ke delivered, tambah stock
                foreach ($purchase->details as $detail) {
                    $product = \App\Models\Product::find($detail->product_id);
                    if ($product) {
                        $product->increment('stock', $detail->quantity);
                    }
                }
            } elseif ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
                // Status berubah dari delivered ke bukan delivered, kurangi stock
                foreach ($purchase->details as $detail) {
                    $product = \App\Models\Product::find($detail->product_id);
                    if ($product) {
                        if ($product->stock < $detail->quantity) {
                            throw new InsufficientStockException($product->name);
                        }
                        $product->decrement('stock', $detail->quantity);
                    }
                }
            }

            return $purchase->fresh()->load(['details.product', 'payments', 'taxes', 'supplier']);
        });
    }


    public function getAll()
    {
        $query = Purchase::with(['user.role', 'details.product.unit', 'payments', 'taxes', 'supplier'])->latest();

        if ($invoice = request()->get('invoice')) {
            $query->where('invoice_number', 'like', "%{$invoice}%");
        }

        if($status = request()->get('status')){
            $query->where('status', 'like', "%{$status}%");;
        }

        if($paymentStatus = request()->get('payment_status')){
            $query->where('payment_status', 'like', "%{$paymentStatus}%");;
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

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function softdelete($id)
    {
        $purchase = Purchase::query()->findOrFail($id);

        $purchase->delete();
        return $purchase;
    }

    public function restore($id)
    {
        $purchase = Purchase::withTrashed()->findOrFail($id);

        if(!$purchase->trashed()){
            throw new \Exception("Cannot restore, purchase is not deleted", 400);
        }

        $purchase->restore();

        $purchase->details()->withTrashed()->each(function ($detail) {
            $detail->restore();
        });

        $purchase->payments()->withTrashed()->each(function ($payment) {
            $payment->restore();
        });
        return $purchase;
    }

    public function trashed()
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

    public function harddelete($id)
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
