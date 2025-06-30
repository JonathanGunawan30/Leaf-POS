<?php

namespace App\Services\Implementations;

use App\Events\StockAlertEvent;
use App\Exceptions\AddressNotFound;
use App\Exceptions\CourierIsBusyException;
use App\Exceptions\CourierNotFoundException;
use App\Exceptions\CustomerNotFound;
use App\Exceptions\DistanceNotFound;
use App\Exceptions\ProductNotFound;
use App\Exceptions\RestoreException;
use App\Exceptions\SaleCannotBeDeletedException;
use App\Exceptions\SaleDetailNotFoundException;
use App\Exceptions\ShipmentNotFoundException;
use App\Exceptions\StockNotEnoughException;
use App\Mail\StockAlertMail;
use App\Services\NotificationService;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalePayment;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Interfaces\SaleService;
use Exception;
use HttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SaleServiceImpl implements SaleService
{
//    public function store($data)
//    {
//        return DB::transaction(function () use ($data) {
//            $totalAmount = 0;
//            $totalTax = 0;
//            $totalShippingCost = 0;
//
//            $saleDetails = [];
//
//            foreach ($data['sale_details'] as $detail) {
//                $product = Product::with('taxes')->findOrFail($detail['product_id']);
//
//                if ($product->stock < $detail['quantity']) {
//                    throw new StockNotEnoughException($product->name);
//                }
//
//                $unitPrice = $product->selling_price;
//                $subTotal = $detail['quantity'] * $unitPrice;
//
//                $totalAmount += $subTotal;
//
//                foreach ($product->taxes as $tax) {
//                    $taxAmount = $subTotal * ($tax->rate / 100);
//                    $totalTax += $taxAmount;
//                }
//
//                $saleDetails[] = [
//                    'product' => $product,
//                    'quantity' => $detail['quantity'],
//                    'unit_price' => $unitPrice,
//                    'sub_total' => $subTotal,
//                    'note' => $detail['note'] ?? null,
//                ];
//            }
//
//            $origin = env('SHIPPING_ORIGIN');
//
//            $customer = Customer::find($data['customer_id']);
//            if (!$customer) {
//                throw new CustomerNotFound();
//            }
//
//            $destination = $customer->address;
//            $totalShippingCost = $this->calculateShippingCost($origin, $destination);
//
//            $grandTotal = ($totalAmount - $data['total_discount']) + $totalTax + $totalShippingCost;
//
//            $totalPayment = collect($data['sale_payments'] ?? [])->sum('amount');
//            $paymentStatus = $this->determinePaymentStatus($totalPayment, $grandTotal);
//
//            $sale = Sale::create([
//                'sale_date' => $data['sale_date'],
//                'total_amount' => $totalAmount,
//                'total_discount' => $data['total_discount'],
//                'total_tax' => $totalTax,
//                'grand_total' => $grandTotal,
//                'payment_status' => $paymentStatus,
//                'status' => $data['status'],
//                'due_date' => $data['due_date'] ?? null,
//                'note' => $data['note'] ?? null,
//                'customer_id' => $data['customer_id'],
//                'user_id' => auth()->id(),
//            ]);
//
//            foreach ($saleDetails as $detail) {
//                SaleDetail::create([
//                    'sale_id' => $sale->id,
//                    'product_id' => $detail['product']->id,
//                    'quantity' => $detail['quantity'],
//                    'unit_price' => $detail['unit_price'],
//                    'sub_total' => $detail['sub_total'],
//                    'note' => $detail['note'],
//                ]);
//
//                $detail['product']->decrement('stock', $detail['quantity']);
//            }
//
//            foreach ($data['sale_payments'] as $payment) {
//                SalePayment::create([
//                    'sale_id' => $sale->id,
//                    'payment_date' => $payment['payment_date'],
//                    'amount' => $payment['amount'],
//                    'due_date' => $payment['due_date'] ?? null,
//                    'status' => $payment['status'],
//                    'payment_method' => $payment['payment_method'],
//                    'note' => $payment['note'] ?? null,
//                ]);
//            }
//
//            if (!empty($data['shipments'])) {
//                foreach ($data['shipments'] as $shipment) {
//
//                    $courier = Courier::find($shipment['courier_id']);
//                    if (!$courier) {
//                        throw new CourierNotFoundException();
//                    }
//
//                    if ($courier->status !== 'available') {
//                        throw new CourierIsBusyException();
//                    }
//
//                    Shipment::create([
//                        'sale_id' => $sale->id,
//                        'courier_id' => $shipment['courier_id'],
//                        'vehicle_type' => $shipment['vehicle_type'],
//                        'vehicle_number' => $shipment['vehicle_number'],
//                        'shipping_date' => $shipment['shipping_date'],
//                        'estimated_arrival_date' => $shipment['estimated_arrival_date'],
//                        'actual_arrival_date' => $shipment['actual_arrival_date'] ?? null,
//                        'status' => $shipment['status'],
//                        'note' => $shipment['note'] ?? null,
//                        'shipping_cost' => $totalShippingCost
//                    ]);
//                }
//            }
//
//            $this->generateNumbers($sale);
//
//            return $sale;
//        });
//
//    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $totalAmount = 0;
            $totalTax = 0;
            $totalShippingCost = 0;

            $saleDetails = [];

            foreach ($data['sale_details'] as $detail) {
                $product = Product::with('taxes')->findOrFail($detail['product_id']);

                if ($data['status'] !== 'indent' && $product->stock < $detail['quantity']) {
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

//            $destination = $customer->address;
//            $totalShippingCost = $this->calculateShippingCost($origin, $destination);
            $totalShippingCost = rand(50000, 200000);

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


                if ($data['status'] !== 'indent') {
                    $previousStock = $detail['product']->stock;
                    $detail['product']->decrement('stock', $detail['quantity']);


                    \App\Models\StockMovement::create([
                        'product_id' => $detail['product']->id,
                        'user_id' => auth()->id(),
                        'reference_type' => \App\Models\Sale::class,
                        'reference_id' => $sale->id,
                        'previous_stock' => $previousStock,
                        'new_stock' => $detail['product']->fresh()->stock,
                        'quantity' => -$detail['quantity'],
                        'movement_type' => 'sale',
                        'notes' => 'Stock decreased due to sale',
                    ]);



                    if ($detail['product']->stock <= $detail['product']->stock_alert) {
                        // Create notification service instance
                        $notificationService = new NotificationService();

                        // Create stock alert notification
                        $notification = $notificationService->createStockAlert($detail['product'], null, false);

                        // Broadcast the event with the notification
                        event(new StockAlertEvent($detail['product'], $notification));

                        $users = User::whereHas('role', function ($query) {
                            $query->whereIn('name', ['Admin', 'Purchasing', 'Inventory']);
                        })->get();

                        foreach ($users as $user) {
                            Mail::to($user->email)->send(new StockAlertMail($detail['product']));
                        }
                    }
                }
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

            if ($data['status'] !== 'indent' && !empty($data['shipments'])) {
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
        $now = now();
        $currentDate = $now->format('Ymd');
        $currentDateTime = $now->format('Y-m-d');
        $year2Digit = $now->format('y');
        $yearRoman = $this->convertToRoman($year2Digit);
        $monthRoman = $this->getMonthInRoman($now->format('n'));
        $random10Digit = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

        if (in_array($sale->status, ['delivered', 'shipped', 'confirmed']) && !$sale->delivery_number) {
            $sale->delivery_number = "SJ/{$currentDate}/{$yearRoman}/{$monthRoman}/{$random10Digit}";
        }

        if ($sale->status == 'delivered') {
            if ($sale->payment_status == 'paid') {
                if (!$sale->invoice_number) {
                    $sale->invoice_number = "INV/{$currentDate}/{$yearRoman}/{$monthRoman}/{$random10Digit}";
                    $sale->invoice_issue_date = $currentDateTime;
                }
            } else {
                if (!$sale->invoice_downpayment_number) {
                    $sale->invoice_downpayment_number = "INV-DP/{$currentDate}/{$yearRoman}/{$monthRoman}/{$random10Digit}";
                    $sale->invoice_downpayment_issue_date = $currentDateTime;
                }
            }
        }

        $sale->save();
    }

    private function getMonthInRoman($month)
    {
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romanMonths[$month] ?? 'I';
    }

    private function convertToRoman($number)
    {
        $romanNumerals = [
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I'
        ];

        $result = '';
        foreach ($romanNumerals as $value => $roman) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }

        return $result;
    }


    private function calculateShippingCost($origin, $destination)
    {
        $originCoords = $this->geocode($origin);
        $destinationCoords = $this->geocode($destination);

        $distance = $this->calculateDistance($originCoords, $destinationCoords);

        $shippingCost = ceil($distance * 5000);
        return $shippingCost;
    }


    private function geocode($address)
    {
        $encodedAddress = urlencode($address);
        $url = "https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json";

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: LeafPOS/1.0 (jgunawan3005@gmail.com)"
            ]
        ];
        $context = stream_context_create($opts);

        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            throw new \Exception("Failed to fetch data from Nominatim");
        }

        $data = json_decode($response, true);

        if (isset($data[0])) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon']
            ];
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

            foreach ($sale->details as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('stock', $detail->quantity);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'reference_type' => \App\Models\Sale::class,
                        'reference_id' => $sale->id,
                        'previous_stock' => $product->stock - $detail->quantity,
                        'new_stock' => $product->stock,
                        'quantity' => $detail->quantity,
                        'movement_type' => 'sale',
                        'notes' => 'Stock reverted due to sale update',
                    ]);

                }
            }
            $originalStatus = $sale->getOriginal('status');

            // 2. Update data sale utama
            $sale->update([
                'sale_date' => $data['sale_date'] ?? $sale->sale_date,
                'total_discount' => $data['total_discount'] ?? $sale->total_discount,
                'status' => $data['status'] ?? $sale->status,
                'due_date' => $data['due_date'] ?? $sale->due_date,
                'note' => $data['note'] ?? $sale->note,
                'customer_id' => $data['customer_id'] ?? $sale->customer_id,
            ]);


            $newStatus = $sale->status;

            if ($originalStatus === 'indent' && $newStatus !== 'indent') {
                foreach ($sale->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if (!$product) {
                        throw new ProductNotFound();
                    }

                    if ($product->stock < $detail->quantity) {
                        throw new StockNotEnoughException($product->name);
                    }

                    $product->decrement('stock', $detail->quantity);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'reference_type' => \App\Models\Sale::class,
                        'reference_id' => $sale->id,
                        'previous_stock' => $product->stock + $detail->quantity,
                        'new_stock' => $product->stock,
                        'quantity' => -$detail->quantity,
                        'movement_type' => 'sale',
                        'notes' => 'Stock decreased due to status update from indent',
                    ]);


                    if ($product->stock <= $product->stock_alert) {
                        // Create notification service instance
                        $notificationService = new NotificationService();

                        // Create stock alert notification
                        $notification = $notificationService->createStockAlert($product, null, false);

                        // Broadcast the event with the notification
                        event(new StockAlertEvent($product, $notification));

                        $users = User::whereIn('role', ['Admin', 'Purchasing', 'Inventory'])->get();
                        foreach ($users as $user) {
                            Mail::to($user->email)->send(new StockAlertMail($product));
                        }
                    }
                }
            }


            $totalAmount = 0;
            $totalTax = 0;

            // 3. Update detail penjualan
            if (isset($data['sale_details'])) {
                $incomingDetails = collect($data['sale_details']);
                $existingDetails = $sale->details()->get();
                $incomingIds = $incomingDetails->pluck('id')->filter()->all();

                foreach ($incomingDetails as $detail) {
                    if (empty($detail['id'])) {
                        throw ValidationException::withMessages([
                            'sale_details' => ['Each detail must include its ID for update.']
                        ]);
                    }

                    $existing = $existingDetails->firstWhere('id', $detail['id']);

                    if (!$existing) {
                        throw new SaleDetailNotFoundException();
                    }

                    $productId = $detail['product_id'] ?? $existing->product_id;
                    $quantity = $detail['quantity'] ?? $existing->quantity;

                    $product = Product::with('taxes')->find($productId);
                    if (!$product) {
                        throw new ProductNotFound();
                    }

                    if ($quantity !== $existing->quantity && $product->stock < $quantity) {
                        throw new StockNotEnoughException($product->name);
                    }

                    $subTotal = $quantity * $product->selling_price;
                    $totalAmount += $subTotal;

                    foreach ($product->taxes as $tax) {
                        $totalTax += $subTotal * ($tax->rate / 100);
                    }

                    // Update detail
                    $existing->update([
                        'product_id' => $productId,
                        'quantity' => $quantity,
                        'unit_price' => $product->selling_price,
                        'sub_total' => $subTotal,
                        'note' => $detail['note'] ?? $existing->note,
                    ]);


                    if ($sale->status !== 'indent') {

                        $product->decrement('stock', $quantity);

                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Sale::class,
                            'reference_id' => $sale->id,
                            'previous_stock' => $product->stock + $quantity,
                            'new_stock' => $product->stock,
                            'quantity' => -$quantity,
                            'movement_type' => 'sale',
                            'notes' => 'Stock decreased due to sale detail update',
                        ]);



                        if ($product->stock <= $product->stock_alert) {
                            // Create notification service instance
                            $notificationService = new NotificationService();

                            // Create stock alert notification
                            $notification = $notificationService->createStockAlert($product, null, false);

                            // Broadcast the event with the notification
                            event(new StockAlertEvent($product, $notification));

                            $users = User::whereIn('role', ['Admin', 'Purchasing', 'Inventory'])->get();
                            foreach ($users as $user) {
                                Mail::to($user->email)->send(new StockAlertMail($product));
                            }
                        }
                    }
                }
            } else {
                $totalAmount = $sale->total_amount;
                $totalTax = $sale->total_tax;
            }

            // 4. Payment Handling - HYBRID APPROACH (RECOMMENDED)
            $totalPayment = 0;
            if (isset($data['sale_payments'])) {
                $incomingPayments = collect($data['sale_payments']);
                $existingPayments = $sale->payments()->get()->keyBy('id');

                $validIncomingIds = [];

                foreach ($incomingPayments as $paymentData) {
                    $totalPayment += $paymentData['amount'];

                    if (!empty($paymentData['id'])) {
                        $paymentId = $paymentData['id'];
                        if ($existingPayments->has($paymentId)) {
                            $existingPayments[$paymentId]->update([
                                'payment_date' => $paymentData['payment_date'],
                                'amount' => $paymentData['amount'],
                                'due_date' => $paymentData['due_date'] ?? $existingPayments[$paymentId]->due_date,
                                'status' => $paymentData['status'],
                                'payment_method' => $paymentData['payment_method'],
                                'note' => $paymentData['note'] ?? $existingPayments[$paymentId]->note,
                            ]);
                            $validIncomingIds[] = $paymentId;
                        }
                    } else {
                        $newPayment = $sale->payments()->create([
                            'payment_date' => $paymentData['payment_date'],
                            'amount' => $paymentData['amount'],
                            'due_date' => $paymentData['due_date'] ?? null,
                            'status' => $paymentData['status'],
                            'payment_method' => $paymentData['payment_method'],
                            'note' => $paymentData['note'] ?? null,
                        ]);
                        $validIncomingIds[] = $newPayment->id;
                    }
                }

                $sale->payments()->whereNotIn('id', $validIncomingIds)->delete();

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


            if (isset($data['shipments'])) {
                $incomingShipments = collect($data['shipments']);
                $first = $incomingShipments->first();

                if (isset($first['id']) && !Shipment::find($first['id'])) {
                    throw new ShipmentNotFoundException();
                }

                foreach ($incomingShipments as $incoming) {
                    if (isset($incoming['id'])) {
                        $shipment = $sale->shipments()->where('id', $incoming['id'])->first();

                        if ($shipment) {
                            $oldStatus = $shipment->status;
                            $newStatus = $incoming['status'] ?? $oldStatus;

                            $updateData = [];

                            foreach (['courier_id', 'vehicle_type', 'vehicle_number', 'shipping_date', 'estimated_arrival_date', 'actual_arrival_date', 'status', 'note'] as $field) {
                                if (array_key_exists($field, $incoming)) {
                                    $updateData[$field] = $incoming[$field];
                                }
                            }

                            $updateData['shipping_cost'] = $totalShippingCost;
                            $shipment->update($updateData);

                            if ($shipment->courier_id && $oldStatus !== $newStatus) {
                                $courier = Courier::find($shipment->courier_id);
                                if ($courier) {
                                    if ($newStatus === 'delivered') {
                                        $courier->update(['status' => 'available']);
                                    } elseif ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
                                        $courier->update(['status' => 'unavailable']);
                                    }
                                }
                            }
                        }
                    } else {
                        $newShipment = $sale->shipments()->create([
                            'courier_id' => $incoming['courier_id'],
                            'vehicle_type' => $incoming['vehicle_type'],
                            'vehicle_number' => $incoming['vehicle_number'],
                            'shipping_date' => $incoming['shipping_date'],
                            'estimated_arrival_date' => $incoming['estimated_arrival_date'],
                            'actual_arrival_date' => $incoming['actual_arrival_date'],
                            'status' => $incoming['status'],
                            'note' => $incoming['note'] ?? null,
                            'shipping_cost' => $totalShippingCost,
                        ]);

                        if ($newShipment->courier_id && $incoming['status'] !== 'delivered') {
                            $courier = Courier::find($newShipment->courier_id);
                            if ($courier) {
                                $courier->update(['status' => 'unavailable']);
                            }
                        }
                    }
                }
            } elseif (isset($data['customer_id'])) {
                foreach ($sale->shipments as $shipment) {
                    $shipment->update(['shipping_cost' => $totalShippingCost]);
                }

                $grandTotal = ($totalAmount - $sale->total_discount) + $totalTax + $totalShippingCost;

                $sale->update([
                    'grand_total' => $grandTotal,
                    'payment_status' => $this->determinePaymentStatus($totalPayment, $grandTotal),
                ]);
            }

            $this->generateNumbers($sale);
            return $sale;
        });
    }

    public function getAll()
    {
        $query = Sale::with([
            'user.role',
            'details.product.unit',
            'details.product.taxes',
            'details.product.category',
            'payments',
            'shipments.courier',
            'customer'
        ])->latest();


        if ($status = request()->get('status')) {
            if ($status === 'all_except_indent') {
                $query->where('status', '!=', 'indent');
            } else {
                $query->where('status', $status);
            }
        }


        if ($search = request()->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('invoice_downpayment_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details.product', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('shipments.courier', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($invoice = request()->get('invoice')) {
            $query->where(function ($q) use ($invoice) {
                $q->where('invoice_number', 'like', "%{$invoice}%")
                    ->orWhere('invoice_downpayment_number', 'like', "%{$invoice}%");
            });
        }

        if ($paymentStatus = request()->get('payment_status')) {
            $query->where('payment_status',$paymentStatus);
        }

        if ($customerName = request()->get('customer_name')) {
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name', 'like', "%{$customerName}%");
            });
        }

        if ($productName = request()->get('product_name')) {
            $query->whereHas('details.product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }

        if ($dueDate = request()->get('due_date')) {
            $query->whereDate('due_date', $dueDate);
        }

        if ($courierName = request()->get('courier_name')) {
            $query->whereHas('shipments.courier', function ($q) use ($courierName) {
                $q->where('name', 'like', "%{$courierName}%");
            });
        }

        if ($shipmentStatus = request()->get('shipment_status')) {
            $query->whereHas('shipments', function ($q) use ($shipmentStatus) {
                $q->where('status', $shipmentStatus);
            });
        }

        if ($saleDateStart = request()->get('sale_date_start')) {
            $query->whereDate('sale_date', '>=', $saleDateStart);
        }

        if ($saleDateEnd = request()->get('sale_date_end')) {
            $query->whereDate('sale_date', '<=', $saleDateEnd);
        }

        if ($dueDateStart = request()->get('due_date_start')) {
            $query->whereDate('due_date', '>=', $dueDateStart);
        }

        if ($dueDateEnd = request()->get('due_date_end')) {
            $query->whereDate('due_date', '<=', $dueDateEnd);
        }


        if ($downpaymentIssueDateStart = request()->get('downpayment_issue_date_start')) {
            $query->whereDate('downpayment_issue_date', '>=', $downpaymentIssueDateStart);
        }

        if ($downpaymentIssueDateEnd = request()->get('downpayment_issue_date_end')) {
            $query->whereDate('downpayment_issue_date', '<=', $downpaymentIssueDateEnd);
        }

        if ($startDate = request()->get('start_date')) {
            $query->whereDate('sale_date', '>=', $startDate);
        }


        if ($endDate = request()->get('end_date')) {
            $query->whereDate('sale_date', '<=', $endDate);
        }

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function softDelete($id, $reason = null)
    {
        $sale = Sale::findOrFail($id);

        return DB::transaction(function () use ($sale, $reason) {
            $hasShippedItems = $sale->shipments()->whereIn('status', ['shipped', 'delivered'])->exists();

            if ($hasShippedItems) {
                throw new SaleCannotBeDeletedException("Cannot delete sale that has been shipped or delivered");
            }

            if ($sale->status !== 'indent') {
                foreach ($sale->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if ($product) {
                        $previousStock = $product->stock;
                        $product->increment('stock', $detail->quantity);

                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'user_id' => auth()->id(),
                            'reference_type' => \App\Models\Sale::class,
                            'reference_id' => $sale->id,
                            'previous_stock' => $previousStock,
                            'new_stock' => $product->stock,
                            'quantity' => $detail->quantity,
                            'movement_type' => 'sale',
                            'notes' => 'Stock returned due to soft delete of sale #' . $sale->id,
                        ]);
                    }
                }
            }

            foreach ($sale->shipments as $shipment) {
                if ($shipment->courier_id && $shipment->status !== 'delivered') {
                    $courier = Courier::find($shipment->courier_id);
                    if ($courier) {
                        $courier->update(['status' => 'available']);
                    }
                }
            }

            $sale->delete();

            return $sale;
        });
    }


    public function restore($id)
    {
        $sale = Sale::withTrashed()
            ->with(['details', 'shipments'])
            ->findOrFail($id);

        return DB::transaction(function () use ($sale) {

            if ($sale->status !== 'indent') {
                foreach ($sale->details as $detail) {
                    $product = Product::find($detail->product_id);
                    if (!$product || $product->stock < $detail->quantity) {
                        throw new SaleCannotBeDeletedException("Unable to restore the sale due to insufficient stock. You may create a new sale or place an indent for the unavailable items.");
                    }
                }
            }

            $sale->restore();
            $sale->load(['details']);

            if ($sale->status !== 'indent') {
                foreach ($sale->details as $detail) {
                    $product = Product::find($detail->product_id);
                    $previousStock = $product->stock;
                    $product->decrement('stock', $detail->quantity);

                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'reference_type' => \App\Models\Sale::class,
                        'reference_id' => $sale->id,
                        'previous_stock' => $previousStock,
                        'new_stock' => $product->stock,
                        'quantity' => -$detail->quantity,
                        'movement_type' => 'sale',
                        'notes' => 'Stock decreased due to restoration of sale #' . $sale->id,
                    ]);
                }
            }

            foreach ($sale->shipments as $shipment) {
                if ($shipment->courier_id && $shipment->status !== 'delivered') {
                    $courier = Courier::find($shipment->courier_id);
                    if ($courier && $courier->status === 'available') {
                        $courier->update(['status' => 'unavailable']);
                    }
                }
            }

            return $sale;
        });
    }





    public function trashed()
    {
        $query = Sale::onlyTrashed()->with([
            'user.role',
            'details.product.unit',
            'details.product.taxes',
            'details.product.category',
            'payments',
            'shipments.courier',
            'customer'
        ])->orderBy('deleted_at', 'desc');

        if ($search = request()->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('invoice_downpayment_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details.product', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('shipments.courier', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($invoice = request()->get('invoice')) {
            $query->where(function ($q) use ($invoice) {
                $q->where('invoice_number', 'like', "%{$invoice}%")
                    ->orWhere('invoice_downpayment_number', 'like', "%{$invoice}%");
            });
        }

        if ($status = request()->get('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = request()->get('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($customerName = request()->get('customer_name')) {
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name', 'like', "%{$customerName}%");
            });
        }

        if ($productName = request()->get('product_name')) {
            $query->whereHas('details.product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }

        if ($dueDate = request()->get('due_date')) {
            $query->whereDate('due_date', $dueDate);
        }

        if ($courierName = request()->get('courier_name')) {
            $query->whereHas('shipments.courier', function ($q) use ($courierName) {
                $q->where('name', 'like', "%{$courierName}%");
            });
        }

        if ($shipmentStatus = request()->get('shipment_status')) {
            $query->whereHas('shipments', function ($q) use ($shipmentStatus) {
                $q->where('status', $shipmentStatus);
            });
        }

        if ($saleDateStart = request()->get('sale_date_start')) {
            $query->whereDate('sale_date', '>=', $saleDateStart);
        }

        if ($saleDateEnd = request()->get('sale_date_end')) {
            $query->whereDate('sale_date', '<=', $saleDateEnd);
        }

        if ($dueDateStart = request()->get('due_date_start')) {
            $query->whereDate('due_date', '>=', $dueDateStart);
        }

        if ($dueDateEnd = request()->get('due_date_end')) {
            $query->whereDate('due_date', '<=', $dueDateEnd);
        }

        if ($downpaymentIssueDateStart = request()->get('downpayment_issue_date_start')) {
            $query->whereDate('downpayment_issue_date', '>=', $downpaymentIssueDateStart);
        }

        if ($downpaymentIssueDateEnd = request()->get('downpayment_issue_date_end')) {
            $query->whereDate('downpayment_issue_date', '<=', $downpaymentIssueDateEnd);
        }

        if ($startDate = request()->get('start_date')) {
            $query->whereDate('sale_date', '>=', $startDate);
        }

        if ($endDate = request()->get('end_date')) {
            $query->whereDate('sale_date', '<=', $endDate);
        }

        $perPage = request()->get('per_page', 10);
        return $query->paginate($perPage);
    }

    public function hardDelete($id)
    {
        $sale = Sale::withTrashed()->findOrFail($id);

        return DB::transaction(function () use ($sale) {
            foreach ($sale->shipments as $shipment) {
                if ($shipment->courier_id && $shipment->status !== 'delivered') {
                    $courier = Courier::find($shipment->courier_id);
                    if ($courier) {
                        $courier->update(['status' => 'available']);
                    }
                }
            }

            $sale->forceDelete();

            return $sale;
        });
    }


}
