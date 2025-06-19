<?php

namespace App\Services\Implementations;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\SalePayment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;
use App\Models\Courier;
use App\Models\User;
use App\Models\Tax;
use App\Models\ShipmentReturn;
use App\Exceptions\ProductNotFoundInSaleException;
use App\Exceptions\ReturnedQuantityExceedsOriginalException;
use App\Exceptions\CourierNotFoundException;
use App\Exceptions\CourierIsBusyException;
use App\Services\Interfaces\SaleReturnService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleReturnServiceImpl implements SaleReturnService
{
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
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
    private function generateSaleReturnInvoiceNumber(): string
    {
        $now = now();

        $tanggal = $now->format('Ymd');
        $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$now->month - 1];
        $tahunRomawi = $this->toRoman((int) $now->format('y'));

        do {
            $randomNumber = str_pad((string) mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $invoiceNumber = "SRT/{$tanggal}/{$tahunRomawi}/{$bulanRomawi}/{$randomNumber}";
        } while (SaleReturn::where('invoice_number_returns', $invoiceNumber)->exists());

        return $invoiceNumber;
    }
    private function generateSaleReturnDeliveryNumber(): string
    {
        $now = now();

        $tanggal = $now->format('Ymd');
        $bulanRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][$now->month - 1];
        $tahunRomawi = $this->toRoman((int) $now->format('y'));

        do {
            $randomNumber = str_pad((string) mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            $deliveryNumber = "SJR/{$tanggal}/{$tahunRomawi}/{$bulanRomawi}/{$randomNumber}";
        } while (SaleReturn::where('delivery_number_returns', $deliveryNumber)->exists());

        return $deliveryNumber;
    }

    public function store($data): SaleReturn
    {
        return DB::transaction(function () use ($data) {
            $originalSale = Sale::with('details.product.taxes', 'customer', 'payments')
                ->where('invoice_number', $data['invoice_number'])
                ->firstOrFail();

            $data['sale_id'] = $originalSale->id;
            $data['customer_id'] = $originalSale->customer_id;
            $data['user_id'] = Auth::user()->id;

            $returnDetailsInput = $data['sale_return_details'];
            $returnPaymentsInput = $data['sale_payments'] ?? [];
            $shipmentReturnsInput = $data['shipment_returns'] ?? [];

            $originalSaleDetails = $originalSale->details->keyBy('product_id');

            $totalAmountReturn = 0;
            $totalTaxReturn = 0;

            $validatedReturnDetails = [];

            foreach ($returnDetailsInput as $detail) {
                $productId = $detail['product_id'];
                $returnedQuantity = $detail['quantity'];

                if (!isset($originalSaleDetails[$productId])) {
                    throw new ProductNotFoundInSaleException("Product with ID {$productId} not found in original sale.");
                }

                $originalDetail = $originalSaleDetails[$productId];
                $product = $originalDetail->product;

                if ($returnedQuantity > $originalDetail->quantity) {
                    throw new ReturnedQuantityExceedsOriginalException(
                        "Returned quantity {$returnedQuantity} for product '{$product->name}' exceeds original sold quantity {$originalDetail->quantity}."
                    );
                }

                $unitPrice = $originalDetail->unit_price;
                $subTotalDetail = $returnedQuantity * $unitPrice;
                $totalAmountReturn += $subTotalDetail;

                foreach ($product->taxes as $tax) {
                    $taxAmount = $subTotalDetail * ($tax->rate / 100);
                    $totalTaxReturn += $taxAmount;
                }

                $validatedReturnDetails[] = [
                    'product_id' => $productId,
                    'quantity' => $returnedQuantity,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotalDetail,
                    'note' => $detail['note'] ?? null,
                ];
            }

            $totalShippingCostForReturn = collect($shipmentReturnsInput)->sum('shipping_cost');


            $grandTotalReturn = ($totalAmountReturn - $data['total_discount']) + $totalTaxReturn;

            if ($data['compensation_method'] === 'refund') {
                $grandTotalReturn += $totalShippingCostForReturn;
            } elseif ($data['compensation_method'] === 'replacement') {
                $grandTotalReturn = $totalShippingCostForReturn;
            }

            $saleReturn = SaleReturn::create([
                'invoice_number' => $originalSale->invoice_number,
                'invoice_number_returns' => $this->generateSaleReturnInvoiceNumber(),
                'delivery_number_returns' => $this->generateSaleReturnDeliveryNumber(),
                'invoice_return_issue_date' => now(),
                'reason' => $data['reason'],
                'compensation_method' => $data['compensation_method'],
                'return_date' => $data['return_date'],
                'total_amount' => $totalAmountReturn,
                'total_tax' => $totalTaxReturn,
                'total_discount' => $data['total_discount'],
                'grand_total' => $grandTotalReturn,
                'status' => $data['status'],
                'payment_status' => $data['payment_status'],
                'user_id' => $data['user_id'],
                'sale_id' => $data['sale_id'],
                'customer_id' => $data['customer_id'],
            ]);

            foreach ($validatedReturnDetails as $detail) {
                SaleReturnDetail::create([
                    'sale_return_id' => $saleReturn->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'sub_total' => $detail['sub_total'],
                    'note' => $detail['note'],
                ]);

                $product = Product::find($detail['product_id']);
                if ($product) {
                    $previousStock = $product->stock;
                    $product->increment('stock', $detail['quantity']);
                    StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'reference_type' => SaleReturn::class,
                        'reference_id' => $saleReturn->id,
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->fresh()->stock,
                        'quantity' => $detail['quantity'],
                        'movement_type' => 'sale_return',
                        'notes' => 'Stock increased due to sale return (customer return).',
                    ]);
                }
            }

            if ($data['compensation_method'] === 'refund') {
                foreach ($returnPaymentsInput as $payment) {
                    SalePayment::create([
                        'sale_id' => $originalSale->id,
                        'sale_return_id' => $saleReturn->id,
                        'payment_date' => $payment['payment_date'],
                        'amount' => -$payment['amount'],
                        'due_date' => $payment['due_date'] ?? null,
                        'status' => $payment['status'],
                        'payment_method' => $payment['payment_method'],
                        'note' => 'Refund for sale return: ' . ($payment['note'] ?? 'No note'),
                    ]);
                }
            }

            if (!empty($shipmentReturnsInput)) {
                foreach ($shipmentReturnsInput as $shipment) {
                    $courier = Courier::find($shipment['courier_id']);
                    if (!$courier) {
                        throw new CourierNotFoundException("Courier with ID {$shipment['courier_id']} not found.");
                    }
                    ShipmentReturn::create([
                        'sale_return_id' => $saleReturn->id,
                        'courier_id' => $shipment['courier_id'],
                        'vehicle_type' => $shipment['vehicle_type'],
                        'vehicle_number' => $shipment['vehicle_number'],
                        'shipping_date' => $shipment['shipping_date'],
                        'estimated_arrival_date' => $shipment['estimated_arrival_date'],
                        'actual_arrival_date' => $shipment['actual_arrival_date'] ?? null,
                        'status' => $shipment['status'],
                        'note' => $shipment['note'] ?? null,
                        'shipping_cost' => $shipment['shipping_cost'] ?? 0
                    ]);
                }
            }

            $totalRefundedAmount = collect($returnPaymentsInput)->sum('amount');
            if ($data['compensation_method'] === 'refund') {
                if ($totalRefundedAmount >= $saleReturn->grand_total) {
                    $saleReturn->payment_status = 'paid';
                } elseif ($totalRefundedAmount > 0) {
                    $saleReturn->payment_status = 'partially_paid';
                } else {
                    $saleReturn->payment_status = 'unpaid';
                }
                $saleReturn->save();
            } else if ($data['compensation_method'] === 'replacement') {
                if ($totalShippingCostForReturn > 0 && $totalRefundedAmount < $totalShippingCostForReturn) {
                    $saleReturn->payment_status = 'unpaid';
                } else {
                    $saleReturn->payment_status = 'paid';
                }
                $saleReturn->save();
            }

            return $saleReturn;
        });
    }
    public function recalculateSaleTotals(Sale $sale): void
    {
        $sale->refresh();
        $sale->load('details.product.taxes');

        $newTotalAmount = $sale->details->sum('sub_total');
        $newTotalTax = 0;

        foreach ($sale->details as $detail) {
            $product = $detail->product;
            $subTotalForDetail = $detail->sub_total;

            foreach ($product->taxes as $tax) {
                $taxAmount = $subTotalForDetail * ($tax->rate / 100);
                $newTotalTax += $taxAmount;
            }
        }
        $newGrandTotal = ($newTotalAmount - $sale->total_discount) + $newTotalTax + $sale->total_shipping_cost;

        $sale->update([
            'total_amount' => $newTotalAmount,
            'total_tax' => $newTotalTax,
            'grand_total' => $newGrandTotal,
        ]);
    }
}
