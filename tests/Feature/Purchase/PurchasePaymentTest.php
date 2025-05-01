<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe("POST /api/purchase-payments", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 30000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();
    });

    it('can store a purchase payment by invoice_number', function () {
        $payload = [
            'invoice_number' => 'INV-001',
            'payment_date' => now()->toDateString(),
            'amount' => 99999999,
            'due_date' => now()->addDays(10)->toDateString(),
            'payment_method' => 'bank_transfer',
            'status' => 'paid',
            'note' => 'cicilan kedua',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchase-payments', $payload);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'payment_date',
                'amount',
                'due_date',
                'payment_method',
                'status',
                'note',
                'invoice_number'
            ]
        ]);

        $this->assertDatabaseHas('purchase_payments', [
            'amount' => 99999999,
            'payment_method' => 'bank_transfer',
            'status' => 'paid',
            'note' => 'cicilan kedua',
        ]);

        $updatedPurchase = \App\Models\Purchase::with([
            'payments',
            'details',
            'supplier',
            'taxes'
        ])->find($this->purchaseId);

        expect($updatedPurchase->payment_status)->toBe('paid');
    });

    it('fails to store payment if invoice_number does not exist', function () {
        $payload = [
            'invoice_number' => 'INV-999',
            'payment_date' => now()->toDateString(),
            'amount' => 50000,
            'due_date' => now()->addDays(5)->toDateString(),
            'payment_method' => 'cash',
            'status' => 'paid',
            'note' => 'invalid invoice',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchase-payments', $payload);

        $response->assertStatus(404);
        expect($response->json('errors.message'))->toBe('Invoice not found');
    });

    it('sets payment_status to paid if total payments equal grand_total', function () {
        $remaining = 10000000;

        $payload = [
            'invoice_number' => 'INV-001',
            'payment_date' => now()->toDateString(),
            'amount' => $remaining,
            'due_date' => now()->addDays(5)->toDateString(),
            'payment_method' => 'cash',
            'status' => 'paid',
            'note' => 'pelunasan',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchase-payments', $payload);
        $response->assertStatus(201);

        $purchase = \App\Models\Purchase::where('invoice_number', 'INV-001')->first();
        expect($purchase->payment_status)->toBe('paid');
    });

    it('sets payment_status still partially paid if total payments under grand total', function () {
        $remaining = 1;

        $payload = [
            'invoice_number' => 'INV-001',
            'payment_date' => now()->toDateString(),
            'amount' => $remaining,
            'due_date' => now()->addDays(5)->toDateString(),
            'payment_method' => 'cash',
            'status' => 'paid',
            'note' => 'pelunasan',
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchase-payments', $payload);
        dump($response->json());
        $response->assertStatus(201);

        $purchase = \App\Models\Purchase::where('invoice_number', 'INV-001')->first();
        expect($purchase->payment_status)->toBe('partially_paid');
    });



});

describe("GET /api/purchase-payments/{id}", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();
    });

    it("Should be success get purchase payment by id", function () {
        $paymentId = $this->originalPaymentIds[0];
        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/{$paymentId}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'invoice_number',
                'amount',
                'payment_date',
                'due_date',
                'payment_method',
                'status',
                'note',
            ]
        ]);

        $responseData = $response->json('data');
        expect($responseData['invoice_number'])->toBe($this->originalPurchase->invoice_number);
        expect($responseData['status'])->toBe('paid');
    });

    it("Should return not found when purchase payment id is invalid", function () {
        $invalidId = 999999;

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/{$invalidId}");

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });

    it("Should return not found when purchase payment id is invalid string" , function () {
        $invalidId = "X-INVALID-ID";

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/{$invalidId}");

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });



});


describe("PATCH /api/purchase-payments/{id}", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();
    });

    it("Should be success update purchase payment by id", function () {
        $paymentId = $this->originalPaymentIds[0];
        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$paymentId}", [
                'status' => 'paid',
                'note' => 'pelunasan'
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'invoice_number',
                'amount',
                'payment_date',
                'due_date',
                'payment_method',
            ]
        ]);

        $payment = \App\Models\PurchasePayment::find($paymentId);
        expect($payment->note)->toBe('pelunasan');
        expect($payment->status)->toBe('paid');

        $purchase = $payment->purchase;
        $totalPaid = $purchase->payments()->sum('amount');
        expect($totalPaid)->toBeGreaterThan(0);

    });

    it("Should update payment amount and change purchase status to paid", function () {
        $paymentId = $this->originalPaymentIds[0];

        $purchase = \App\Models\Purchase::find($this->purchaseId);
        $grandTotal = $purchase->grand_total;

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$paymentId}", [
                'amount' => $grandTotal,
                'status' => 'paid',
                'note' => 'lunas'
            ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'amount' => $grandTotal,
            'note' => 'lunas',
            'status' => 'paid'
        ]);

        $payment = \App\Models\PurchasePayment::find($paymentId);
        expect((int) $payment->amount)->toBe((int) $grandTotal);
        expect($payment->note)->toBe('lunas');

        $updatedPurchase = $payment->purchase;
        expect($updatedPurchase->payment_status)->toBe('paid');
    });

    it("Should return 404 when trying to update non-existent purchase payment", function () {
        $invalidId = 999999;

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$invalidId}", [
                'status' => 'paid',
                'note' => 'testing not found'
            ]);

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });

    it("Should return 404 when trying to update non-existent purchase payment even tho id is invalid string", function () {
        $invalidId = "X-INVALID-ID";

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$invalidId}", [
                'status' => 'paid',
                'note' => 'testing not found'
            ]);

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });


});

describe("GET /api/purchase-payments", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

        $this->additionalPayments = collect();

        for ($i = 1; $i <= 50; $i++) {
            $payment = \App\Models\PurchasePayment::create([
                'purchase_id' => $this->purchaseId,
                'payment_date' => now()->subDays($i)->toDateString(),
                'amount' => 10000 + ($i * 100),
                'due_date' => now()->addDays(10 + $i)->toDateString(),
                'payment_method' => 'bank_transfer',
                'status' => 'paid',
                'note' => "Auto Payment {$i}"
            ]);

            $this->additionalPayments->push($payment->id);
        }

    });

    it("Should return paginated list of purchase payments", function () {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/purchase-payments?page=2&per_page=10');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total'
            ],
            'message',
            'statusCode'
        ]);

        $json = $response->json();
        expect($json['meta']['current_page'])->toBe(2);
        expect($json['meta']['per_page'])->toBe(10);
        expect(count($json['data']))->toBeLessThanOrEqual(10);
    });

});

describe("DELETE /api/purchase-payments/{id}", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1770000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

    });

    it("Should be success delete purchase payment and recalculate payment status", function () {
        $paymentId = $this->originalPaymentIds[0];

        $purchase = \App\Models\Purchase::find($this->purchaseId);
        expect($purchase->payment_status)->toBe('paid');

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$paymentId}");

        $response->assertOk();

        $deletedPayment = \App\Models\PurchasePayment::withTrashed()->find($paymentId);
        expect($deletedPayment)->not->toBeNull();
        expect($deletedPayment->trashed())->toBeTrue();

        $purchase->refresh();
        expect($purchase->payment_status)->toBe('unpaid');
    });
    it("Should return 404 when deleting non-existing payment", function () {
        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/999999");

        $response->assertNotFound();
    });

    it("Should return 404 when deleting non-existing payment even tho id is invalid string", function () {
        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/X-INVALID-ID");

        $response->assertNotFound();
    });


});

describe("PATCH /api/purchase-payments/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 999999999,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];


        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

    });

    it("Should restore soft deleted payment and recalculate to paid", function () {
        $paymentId = $this->originalPaymentIds[0];

        $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$paymentId}")
            ->assertOk();

        $payment = \App\Models\PurchasePayment::withTrashed()->find($paymentId);
        expect($payment->trashed())->toBeTrue();

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$paymentId}/restore");

        $response->assertOk();

        $this->originalPurchase->refresh();

        $restoredPayment = \App\Models\PurchasePayment::find($paymentId);
        expect($restoredPayment)->not->toBeNull();
        expect($this->originalPurchase->payment_status)->toBe('paid');
    });

    it("Should return 404 when restoring non-existing payment", function () {
        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/999999/restore");

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });

    it("Should return 404 when restoring non-existing payment even tho id is invalid string", function () {
        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/X-INVALID-ID/restore");

        $response->assertNotFound();
        expect($response->json('errors.message'))->toBe('Payment not found');
    });

    it("Should be fail restore if payment is not deleted", function () {
        $paymentId = $this->originalPaymentIds[0];

        $response = $this->actingAs($this->adminUser)
            ->patchJson("/api/purchase-payments/{$paymentId}/restore");

        expect($response->json("errors.message"))->toBe("Cannot restore, payment is not deleted");
    });

});


describe("DELETE /api/purchase-payments/{id}/force", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1770000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

    });

    it("Should hard delete payment and recalculate payment status", function () {
        $paymentId = $this->originalPaymentIds[0];

        $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$paymentId}")
            ->assertOk();

        $payment = \App\Models\PurchasePayment::withTrashed()->find($paymentId);
        expect($payment->trashed())->toBeTrue();

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$paymentId}/force");


        $payment = \App\Models\PurchasePayment::withTrashed()->find($paymentId);
        expect($payment)->toBeNull();

        $this->originalPurchase->refresh();

        expect($this->originalPurchase->payment_status)->toBe('unpaid');
    });

    it("Should throw error if trying to hard delete payment that is not trashed", function () {
        $paymentId = $this->originalPaymentIds[0];

        $payment = \App\Models\PurchasePayment::find($paymentId);
        expect($payment->trashed())->toBeFalse();

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$paymentId}/force");

        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                'message' => 'Cannot hard delete, payment is not deleted'
            ]
        ]);
    });

    it("Should return 404 if payment to be hard deleted is not found", function () {
        $nonExistentPaymentId = 9999;

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$nonExistentPaymentId}/force");

        $response->assertNotFound();
        $response->assertJson([
            "errors" => [
                "message" => "Payment not found"
            ]
        ]);
    });

    it("Should return 404 if payment to be hard deleted is not found even id is invalid string", function () {
        $nonExistentPaymentId = "X-INVALID-ID";

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/purchase-payments/{$nonExistentPaymentId}/force");

        $response->assertNotFound();
        $response->assertJson([
            "errors" => [
                "message" => "Payment not found"
            ]
        ]);
    });
});

describe("GET /api/purchase-payments/trashed", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                // 10 payment entries, not just 1 or 2
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1770000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'second payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'third payment'
                ],
                // Add more payments for testing pagination
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'fourth payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'fifth payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 400000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'sixth payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 600000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'seventh payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 800000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'eighth payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 250000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'ninth payment'
                ],
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'tenth payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

        $this->originalPurchase->payments->take(5)->each(function ($payment) {
            $payment->delete();
        });
    });


    it("Should return 200 and list of trashed payments", function () {
        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/trashed");
        $response->assertOk();

        $responseData = $response->json();
        $this->assertCount(5, $responseData['data']);
    });


    it("Should return 200 and list of trashed payments with page", function () {
        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/trashed?per_page=1");
        $response->assertOk();

        $responseData = $response->json();
        $this->assertCount(1, $responseData['data']);
    });

    it("Should return an empty list if no trashed payments", function () {

        $this->originalPurchase->payments->each(function ($payment) {
            $payment->forceDelete();
        });

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/purchase-payments/trashed?per_page=2");

        $response->assertOk();
        $response->assertJson([
            'data' => []
        ]);
    });
});

