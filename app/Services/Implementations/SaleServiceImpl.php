<?php

namespace App\Services\Implementations;

use App\Exceptions\AddressNotFound;
use App\Exceptions\CourierIsBusyException;
use App\Exceptions\CourierNotFoundException;
use App\Exceptions\CustomerNotFound;
use App\Exceptions\DistanceNotFound;
use App\Exceptions\ProductNotFound;
use App\Exceptions\ShipmentNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use App\Models\Shipment;
use App\Services\Interfaces\SaleService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleServiceImpl implements SaleService
{
    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            $totalTax = 0;
            $totalShippingCost = 0;

            $saleDetails = [];

            foreach ($data['sale_details'] as $detail) {
                $product = Product::with('taxes')->findOrFail($detail['product_id']);

                if ($product->stock < $detail['quantity']) {
                    throw new StockNotEnoughException($product->name);
                }

                $unitPrice = $product->selling_price;
                $subTotal = $detail['quantity'] * $unitPrice;

                $totalAmount += $subTotal;

                foreach ($product->taxes as $tax) {
                    $taxAmount = $subTotal * ($tax->rate / 100);
                    $totalTax += $taxAmount;
                }

                $saleDetails[] = [
                    'product' => $product,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                    'note' => $detail['note'] ?? null,
                ];
            }

            $origin = env('SHIPPING_ORIGIN');

            $customer = Customer::find($data['customer_id']);
            if (!$customer) {
                throw new CustomerNotFound();
            }

            $destination = $customer->address;
            $totalShippingCost = $this->calculateShippingCost($origin, $destination);

            $grandTotal = ($totalAmount - $data['total_discount']) + $totalTax + $totalShippingCost;

            $totalPayment = collect($data['sale_payments'] ?? [])->sum('amount');
            $paymentStatus = $this->determinePaymentStatus($totalPayment, $grandTotal);

            $sale = Sale::create([
                'sale_date' => $data['sale_date'],
                'total_amount' => $totalAmount,
                'total_discount' => $data['total_discount'],
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'payment_status' => $paymentStatus,
                'status' => $data['status'],
                'due_date' => $data['due_date'] ?? null,
                'note' => $data['note'] ?? null,
                'customer_id' => $data['customer_id'],
                'user_id' => auth()->id(),
            ]);

            foreach ($saleDetails as $detail) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $detail['product']->id,
                    'quantity' => $detail['quantity'],
                    'unit_price' => $detail['unit_price'],
                    'sub_total' => $detail['sub_total'],
                    'note' => $detail['note'],
                ]);

                $detail['product']->decrement('stock', $detail['quantity']);
            }

            foreach ($data['sale_payments'] as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_date' => $payment['payment_date'],
                    'amount' => $payment['amount'],
                    'due_date' => $payment['due_date'] ?? null,
                    'status' => $payment['status'],
                    'payment_method' => $payment['payment_method'],
                    'note' => $payment['note'] ?? null,
                ]);
            }

            if (!empty($data['shipments'])) {
                foreach ($data['shipments'] as $shipment) {

                    $courier = Courier::find($shipment['courier_id']);
                    if (!$courier) {
                        throw new CourierNotFoundException();
                    }

                    if ($courier->status !== 'available') {
                        throw new CourierIsBusyException();
                    }

                    Shipment::create([
                        'sale_id' => $sale->id,
                        'courier_id' => $shipment['courier_id'],
                        'vehicle_type' => $shipment['vehicle_type'],
                        'vehicle_number' => $shipment['vehicle_number'],
                        'shipping_date' => $shipment['shipping_date'],
                        'estimated_arrival_date' => $shipment['estimated_arrival_date'],
                        'actual_arrival_date' => $shipment['actual_arrival_date'] ?? null,
                        'status' => $shipment['status'],
                        'note' => $shipment['note'] ?? null,
                        'shipping_cost' => $totalShippingCost
                    ]);
                }
            }

            $this->generateNumbers($sale);

            return $sale;
        });

    }


    private function determinePaymentStatus(float $totalPayment, float $grandTotal): string
    {
        if ($totalPayment >= $grandTotal) {
            return 'paid';
        } elseif ($totalPayment > 0) {
            return 'partially_paid';
        } else {
            return 'unpaid';
        }
    }

    private function generateNumbers(Sale $sale)
    {
        $now = now()->format('Ymd');
        $random = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $currentDateTime = now()->format('Y-m-d');

        if ($sale->status == 'delivered' && !$sale->delivery_number) {
            $sale->delivery_number = 'SJ-' . $now . '-' . $random;
        }

        if ($sale->status == 'shipped' && !$sale->delivery_number) {
            $sale->delivery_number = 'SJ-' . $now . '-' . $random;
        }

        if ($sale->status == 'confirmed') {
            $sale->delivery_number = 'SJ-' . $now . '-' . $random;
        }

        if ($sale->status == 'delivered') {
            if ($sale->payment_status == 'paid') {
                $sale->invoice_number = 'INV-' . $now . '-' . $random;
                $sale->invoice_issue_date = $currentDateTime;
            } else {
                $sale->invoice_downpayment_number = 'INV-DP-' . $now . '-' . $random;
                $sale->invoice_downpayment_issue_date = $currentDateTime;
            }


        }

        $sale->save();
    }


    private function calculateShippingCost($origin, $destination)
    {
        $originCoords = $this->geocode($origin);
        $destinationCoords = $this->geocode($destination);

        $distance = $this->calculateDistance($originCoords, $destinationCoords);

        $shippingCost = $distance * 5000;
        return $shippingCost;
    }

    private function geocode($address)
    {
        $encodedAddress = urlencode($address);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "User-Agent: MyAppName/1.0 (contact@myapp.com)"
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data[0])) {
            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];
            return ['lat' => $lat, 'lon' => $lon];
        } else {
            throw new AddressNotFound();
        }
    }


    private function calculateDistance($originCoords, $destinationCoords)
    {
        $originLat = $originCoords['lat'];
        $originLon = $originCoords['lon'];
        $destinationLat = $destinationCoords['lat'];
        $destinationLon = $destinationCoords['lon'];

        $url = "http://router.project-osrm.org/route/v1/driving/{$originLon},{$originLat};{$destinationLon},{$destinationLat}?overview=false";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['routes'][0]['legs'][0]['distance'])) {
            return $data['routes'][0]['legs'][0]['distance'] / 1000;
        } else {
            throw new DistanceNotFound();
        }
    }

    public function show($id)
    {
        $sale = Sale::findOrFail($id);

        return $sale;
    }

    public function update($data, $id)
    {
        $sale = Sale::findOrFail($id);

        return DB::transaction(function () use ($sale, $data) {
            // 1. Kembalikan semua stok lama terlebih dahulu
            foreach ($sale->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->quantity);
                }
            }

            // 2. Update data sale utama
            $sale->update([
                'sale_date' => $data['sale_date'] ?? $sale->sale_date,
                'total_discount' => $data['total_discount'] ?? $sale->total_discount,
                'status' => $data['status'] ?? $sale->status,
                'due_date' => $data['due_date'] ?? $sale->due_date,
                'note' => $data['note'] ?? $sale->note,
                'customer_id' => $data['customer_id'] ?? $sale->customer_id,
            ]);

            $totalAmount = 0;
            $totalTax = 0;

            // 3. Update detail penjualan
            if (isset($data['sale_details'])) {
                $incomingDetails = collect($data['sale_details']);
                $existingDetails = $sale->details()->get();
                $incomingIds = $incomingDetails->pluck('id')->filter()->all();

                // Hapus detail yang tidak ada lagi di input
                $existingDetails->each(function ($detail) use ($incomingIds) {
                    if (!in_array($detail->id, $incomingIds)) {
                        $detail->forceDelete(); // stok sudah dikembalikan di awal
                    }
                });

                foreach ($incomingDetails as $detail) {
                    $existing = $sale->details()->where('id', $detail['id'] ?? null)->first();
                    $product = Product::with('taxes')->find($detail['product_id']);
                    if (!$product) {
                        throw new ProductNotFound();
                    }

                    if ($product->stock < $detail['quantity']) {
                        throw new StockNotEnoughException($product->name);
                    }

                    $subTotal = $detail['quantity'] * $product->selling_price;
                    $totalAmount += $subTotal;

                    foreach ($product->taxes as $tax) {
                        $totalTax += $subTotal * ($tax->rate / 100);
                    }

                    if ($existing) {
                        if ($existing->product_id != $detail['product_id']) {
                            $existing->forceDelete();
                            $sale->details()->create([
                                'product_id' => $product->id,
                                'quantity' => $detail['quantity'],
                                'unit_price' => $product->selling_price,
                                'sub_total' => $subTotal,
                                'note' => $detail['note'] ?? null,
                            ]);
                        } else {
                            $existing->update([
                                'quantity' => $detail['quantity'],
                                'unit_price' => $product->selling_price,
                                'sub_total' => $subTotal,
                                'note' => $detail['note'] ?? $existing->note,
                            ]);
                        }
                    } else {
                        $sale->details()->create([
                            'product_id' => $product->id,
                            'quantity' => $detail['quantity'],
                            'unit_price' => $product->selling_price,
                            'sub_total' => $subTotal,
                            'note' => $detail['note'] ?? null,
                        ]);
                    }

                    // Kurangi stok baru
                    $product->decrement('stock', $detail['quantity']);
                }
            } else {
                $totalAmount = $sale->total_amount;
                $totalTax = $sale->total_tax;
            }

            // 4. Payment Handling
            $totalPayment = 0;
            if (isset($data['sale_payments'])) {
                $incomingPayments = collect($data['sale_payments']);
                $existingPayments = $sale->payments()->get();

                $incomingPayments->each(function ($payment) use ($sale, &$totalPayment) {
                    $totalPayment += $payment['amount'];
                    if (isset($payment['id'])) {
                        $existing = $sale->payments()->where('id', $payment['id'])->first();
                        if ($existing) {
                            $existing->update([
                                'payment_date' => $payment['payment_date'],
                                'amount' => $payment['amount'],
                                'due_date' => $payment['due_date'] ?? $existing->due_date,
                                'status' => $payment['status'],
                                'payment_method' => $payment['payment_method'],
                                'note' => $payment['note'] ?? $existing->note,
                            ]);
                        }
                    } else {
                        $sale->payments()->create([
                            'payment_date' => $payment['payment_date'],
                            'amount' => $payment['amount'],
                            'due_date' => $payment['due_date'] ?? null,
                            'status' => $payment['status'],
                            'payment_method' => $payment['payment_method'],
                            'note' => $payment['note'] ?? null,
                        ]);
                    }
                });

                $incomingIds = $incomingPayments->pluck('id')->filter()->all();
                $sale->payments()->whereNotIn('id', $incomingIds)->get()->each->forceDelete();
            } else {
                $totalPayment = $sale->payments()->sum('amount');
            }

            $origin = env('SHIPPING_ORIGIN');
            $customer = Customer::find($data['customer_id'] ?? $sale->customer_id);
            if (!$customer) {
                throw new CustomerNotFound();
            }

            $destination = $customer->address;
            $totalShippingCost = $this->calculateShippingCost($origin, $destination);
            $grandTotal = ($totalAmount - $sale->total_discount) + $totalTax + $totalShippingCost;
            $paymentStatus = $this->determinePaymentStatus($totalPayment, $grandTotal);

            $sale->update([
                'total_amount' => $totalAmount,
                'total_tax' => $totalTax,
                'grand_total' => $grandTotal,
                'payment_status' => $paymentStatus,
            ]);
            // 6. Shipment Handling
            if (isset($data['shipments'])) {
                $incomingShipments = collect($data['shipments']);
                if(!Shipment::find($incomingShipments->first()['id'])){
                    throw new ShipmentNotFoundException();
                }

                foreach ($incomingShipments as $incoming) {
                    if (isset($incoming['id'])) {
                        $shipment = $sale->shipments()->where('id', $incoming['id'])->first();

                        if ($shipment) {
                            $updateData = [];

                            foreach (['courier_id', 'vehicle_type', 'vehicle_number', 'shipping_date', 'estimated_arrival_date', 'actual_arrival_date', 'status', 'note'] as $field) {
                                if (array_key_exists($field, $incoming)) {
                                    $updateData[$field] = $incoming[$field];
                                }
                            }

                            // Tambahkan shipping_cost kalau kamu selalu ingin update itu
                            $updateData['shipping_cost'] = $totalShippingCost;

                            $shipment->update($updateData);
                        }
                    } else {
                        // Handle create baru jika tidak ada id
                        $sale->shipments()->create([
                            'courier_id' => $incoming['courier_id'],
                            'vehicle_type' => $incoming['vehicle_type'],
                            'vehicle_number' => $incoming['vehicle_number'],
                            'shipping_date' => $incoming['shipping_date'],
                            'estimated_arrival_date' => $incoming['estimated_arrival_date'],
                            'actual_arrival_date' => $incoming['actual_arrival_date'] ?? null,
                            'status' => $incoming['status'],
                            'note' => $incoming['note'] ?? null,
                            'shipping_cost' => $totalShippingCost,
                        ]);
                    }
                }
            }





            $this->generateNumbers($sale);
            return $sale;
        });
    }
}
