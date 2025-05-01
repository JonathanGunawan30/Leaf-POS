<?php

namespace App\Services\Implementations;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Services\Interfaces\PurchasePaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchasePaymentServiceImpl implements PurchasePaymentService
{
    public function create($data)
    {
        $purchase = Purchase::query()->where('invoice_number', $data['invoice_number'])->first();

        if (!$purchase) {
            throw new ModelNotFoundException('Invoice not found', 404);
        }

        $data['purchase_id'] = $purchase->id;
        unset($data['invoice_number']);

        $payment = PurchasePayment::create($data);
        $this->recalculatePaymentStatus($purchase);

        return $payment;
    }

    public function show($id)
    {
        return PurchasePayment::findOrFail($id);
    }

    public function update($data, $id)
    {
        $payment = PurchasePayment::findOrFail($id);
        $payment->update($data);

        $purchase = $payment->purchase;
        $this->recalculatePaymentStatus($purchase);

        return $payment;
    }

    public function getAll()
    {
        return PurchasePayment::with('purchase')
            ->orderByDesc('payment_date')
            ->paginate(request('per_page', 10));

    }

    public function softdelete($id)
    {
        $payment = PurchasePayment::findOrFail($id);
        $purchase = $payment->purchase;

        $payment->delete();

        $this->recalculatePaymentStatus($purchase);

        return $payment;
    }

    public function recalculatePaymentStatus(Purchase $purchase)
    {
        $totalPaid = $purchase->payments()->sum('amount');
        $totalDue = $purchase->grand_total;

        if ($totalPaid >= $totalDue) {
            $purchase->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $purchase->payment_status = 'partially_paid';
        } else {
            $purchase->payment_status = 'unpaid';
        }

        $purchase->save();
    }

    public function restore($id)
    {
        $payment = PurchasePayment::withTrashed()->findOrFail($id);
        if(!$payment->trashed()){
            throw new \Exception("Cannot restore, payment is not deleted", 400);
        }
        $purchase = $payment->purchase;
        $payment->restore();
        $this->recalculatePaymentStatus($purchase);
        return $payment;
    }

    public function harddelete($id)
    {
        $payment = PurchasePayment::withTrashed()->findOrFail($id);

        if(!$payment->trashed()){
            throw new \Exception("Cannot hard delete, payment is not deleted", 400);
        }

        $purchase = $payment->purchase;
        $payment->forceDelete();
        $this->recalculatePaymentStatus($purchase);
        return $payment;
    }

    public function trashed()
    {
        return PurchasePayment::onlyTrashed()
            ->with('purchase')
            ->latest()
            ->paginate(request('per_page', 10));
    }

}
