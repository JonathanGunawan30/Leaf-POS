<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//uses(RefreshDatabase::class);

describe("POST /api/purchases", function () {
    beforeEach(function () {
        // Buat role dan user (admin)
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        // Create unit, category, supplier
        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        // Create 3 products
        $this->products = Product::factory()
            ->count(50)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        // Create 2 taxes
        $this->taxes = Tax::factory()->count(2)->create();
    });

    it("should create purchase with details and payments", function () {
        $payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,

            'purchase_details' => $this->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
                    'note' => 'test note'
                ];
            })->toArray(),

            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],

            'taxes' => $this->taxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $payload);
        $response->assertStatus(201);
        expect($response->json('data.purchase_details'))->toHaveCount(3);
        expect($response->json('data.purchase_payments'))->toHaveCount(1);
        expect($response->json('data.taxes'))->toHaveCount(2);
        expect($response->json('data.total_amount'))->not->toBeNull();
        expect($response->json('data.total_discount'))->toBe(50000);
        expect($response->json('data.shipping_amount'))->toBe(20000);
        expect($response->json('data.payment_status'))->toBe('partially_paid');
        expect($response->json('data.status'))->toBe('confirmed');
        expect($response->json('data.due_date'))->toBe(now()->addDays(10)->toDateString());
        expect($response->json('data.estimated_arrival_date'))->toBe(now()->addDays(5)->toDateString());

    });
//
//    it("should create purchase with details and payments auto update payment status if amount payment is above or equal grand total", function () {
//        $payload = [
//            'invoice_number' => 'INV-001',
//            'purchase_date' => now()->toDateString(),
//            'total_discount' => 50000,
//            'shipping_amount' => 20000,
//            'status' => 'confirmed',
//            'due_date' => now()->addDays(10)->toDateString(),
//            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
//            'supplier_id' => $this->supplier->id,
//
//            'purchase_details' => $this->products->map(function ($product) {
//                return [
//                    'product_id' => $product->id,
//                    'quantity' => 5,
//                    'unit_price' => 100000,
//                    'sub_total' => 500000,
//                    'note' => 'test note'
//                ];
//            })->toArray(),
//
//            'purchase_payments' => [
//                [
//                    'payment_date' => now()->toDateString(),
//                    'amount' => 999999999999,
//                    'due_date' => now()->addDays(10)->toDateString(),
//                    'payment_method' => 'bank_transfer',
//                    'status' => 'paid',
//                    'note' => 'first payment'
//                ]
//            ],
//
//            'taxes' => $this->taxes->map(function ($tax) {
//                return [
//                    'tax_id' => $tax->id
//                ];
//            })->toArray()
//        ];
//
//        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $payload);
//        $response->assertStatus(201);
//        expect($response->json('data.purchase_details'))->toHaveCount(3);
//        expect($response->json('data.purchase_payments'))->toHaveCount(1);
//        expect($response->json('data.taxes'))->toHaveCount(2);
//        expect($response->json('data.total_amount'))->not->toBeNull();
//        expect($response->json('data.total_discount'))->toBe(50000);
//        expect($response->json('data.shipping_amount'))->toBe(20000);
//        expect($response->json('data.payment_status'))->toBe('paid');
//        expect($response->json('data.status'))->toBe('confirmed');
//        expect($response->json('data.due_date'))->toBe(now()->addDays(10)->toDateString());
//        expect($response->json('data.estimated_arrival_date'))->toBe(now()->addDays(5)->toDateString());
//
//    });
//    it("should return error when purchase details are missing", function () {
//        $payload = [
//            'invoice_number' => 'INV-002',
//            'purchase_date' => now()->toDateString(),
//            'total_amount' => 500000,
//            'total_discount' => 50000,
//            'shipping_amount' => 20000,
//            'status' => 'confirmed',
//            'payment_status' => 'partially_paid',
//            'due_date' => now()->addDays(10)->toDateString(),
//            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
//            'supplier_id' => $this->supplier->id,
//            'purchase_details' => [],
//            'purchase_payments' => [],
//            'taxes' => []
//        ];
//
//        $response = $this->actingAs($this->adminUser )->postJson('/api/purchases', $payload);
//        $response->assertStatus(400);
//        expect($response->json('errors.message'))->toHaveKeys(["purchase_details", "purchase_payments", "taxes"]);
//    });
//
//    it("Should be fail if inv number already exists", function (){
//        $payload = [
//            'invoice_number' => 'INV-001',
//            'purchase_date' => now()->toDateString(),
//            'total_amount' => 500000,
//            'total_discount' => 50000,
//            'shipping_amount' => 20000,
//            'status' => 'confirmed',
//            'payment_status' => 'partially_paid',
//            'due_date' => now()->addDays(10)->toDateString(),
//            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
//            'supplier_id' => $this->supplier->id,
//
//            'purchase_details' => $this->products->map(function ($product) {
//                return [
//                    'product_id' => $product->id,
//                    'quantity' => 5,
//                    'unit_price' => 100000,
//                    'sub_total' => 500000,
//                    'note' => 'test note'
//                ];
//            })->toArray(),
//
//            'purchase_payments' => [
//                [
//                    'payment_date' => now()->toDateString(),
//                    'amount' => 300000,
//                    'due_date' => now()->addDays(10)->toDateString(),
//                    'payment_method' => 'bank_transfer',
//                    'status' => 'paid',
//                    'note' => 'first payment'
//                ]
//            ],
//
//            'taxes' => $this->taxes->map(function ($tax) {
//                return [
//                    'tax_id' => $tax->id
//                ];
//            })->toArray()
//        ];
//
//        $payload2 = [
//            'invoice_number' => 'INV-001',
//            'purchase_date' => now()->toDateString(),
//            'total_amount' => 500000,
//            'total_discount' => 50000,
//            'shipping_amount' => 20000,
//            'status' => 'confirmed',
//            'payment_status' => 'partially_paid',
//            'due_date' => now()->addDays(10)->toDateString(),
//            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
//            'supplier_id' => $this->supplier->id,
//
//            'purchase_details' => $this->products->map(function ($product) {
//                return [
//                    'product_id' => $product->id,
//                    'quantity' => 5,
//                    'unit_price' => 100000,
//                    'sub_total' => 500000,
//                    'note' => 'test note'
//                ];
//            })->toArray(),
//
//            'purchase_payments' => [
//                [
//                    'payment_date' => now()->toDateString(),
//                    'amount' => 300000,
//                    'due_date' => now()->addDays(10)->toDateString(),
//                    'payment_method' => 'bank_transfer',
//                    'status' => 'paid',
//                    'note' => 'first payment'
//                ]
//            ],
//
//            'taxes' => $this->taxes->map(function ($tax) {
//                return [
//                    'tax_id' => $tax->id
//                ];
//            })->toArray()
//        ];
//
//        $this->actingAs($this->adminUser )->postJson('/api/purchases', $payload);
//        $response = $this->actingAs($this->adminUser )->postJson('/api/purchases', $payload2);
//        $response->assertStatus(400);
//        expect($response->json('errors.message'))->toHaveKeys(["invoice_number"]);
//    });
//
//    it('Should increase product stock if purchase status is delivered', function () {
//        $initialStocks = $this->products->mapWithKeys(function ($product) {
//            return [$product->id => $product->stock];
//        });
//
//        $payload = [
//            'invoice_number' => 'INV-DELIVERED-001',
//            'purchase_date' => now()->toDateString(),
//            'total_amount' => 500000,
//            'total_discount' => 50000,
//            'shipping_amount' => 20000,
//            'status' => 'delivered',
//            'payment_status' => 'partially_paid',
//            'due_date' => now()->addDays(10)->toDateString(),
//            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
//            'supplier_id' => $this->supplier->id,
//
//            'purchase_details' => $this->products->map(function ($product) {
//                return [
//                    'product_id' => $product->id,
//                    'quantity' => 5,
//                    'sub_total' => 500000,
//                    'note' => 'test note'
//                ];
//            })->toArray(),
//
//            'purchase_payments' => [
//                [
//                    'payment_date' => now()->toDateString(),
//                    'amount' => 300000,
//                    'due_date' => now()->addDays(10)->toDateString(),
//                    'payment_method' => 'bank_transfer',
//                    'status' => 'paid',
//                    'note' => 'first payment'
//                ]
//            ],
//
//            'taxes' => $this->taxes->map(function ($tax) {
//                return [
//                    'tax_id' => $tax->id
//                ];
//            })->toArray()
//        ];
//
//        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $payload);
//        $response->assertStatus(201);
//
//        foreach ($this->products as $product) {
//            $product->refresh();
//
//            expect($product->stock)->toBe($initialStocks[$product->id] + 5);
//        }
//    });


});

describe("GET /api/purchases/{id}", function () {
    beforeEach(function () {
        // Buat role dan user (admin)
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        // Create unit, category, supplier
        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        // Create 3 products
        $this->products = Product::factory()
            ->count(3)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        // Create 2 taxes
        $this->taxes = Tax::factory()->count(2)->create();

        $payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,

            'purchase_details' => $this->products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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

            'taxes' => $this->taxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $payload);
        $responseData = $response->json();

        $this->purchaseId = $responseData['data']['id'];

    });

    it("Should be success get puchase detail by id", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->getJson("/api/purchases/{$this->purchaseId}");
        $response->assertOk();
        expect($response->json('data.invoice_number'))->toBe('INV-001');
        expect($response->json('data.purchase_date'))->toBe(now()->toDateString());
        expect($response->json('data.total_discount'))->toContain(50000);
        expect($response->json('data.shipping_amount'))->toContain(20000);
        expect($response->json('data.status'))->toBe('confirmed');
    });

    it("Should be fail get puchase detail by id if not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->getJson("/api/purchases/999");

        expect($response->json('errors.message'))->toBe('Purchase not found');
    });

    it("Should be fail get puchase detail by id if not found even if id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->getJson("/api/purchases/X-INVALID-ID");

        expect($response->json('errors.message'))->toBe('Purchase not found');
    });
});


describe("PATCH /api/purchases/{id}", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->newSupplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);
        $this->newProducts = $this->products->skip(3)->take(2);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);
        $this->newTax = $this->taxes->last();

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,

            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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

    it("Should be success update only basic fields", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $updatePayload = [
            'invoice_number' => 'INV-001-UPDATED',
            'status' => 'shipped'
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertOk();
        $response->assertJsonPath('data.invoice_number', 'INV-001-UPDATED');
        $response->assertJsonPath('data.status', 'shipped');

        expect($response->json("data.total_discount"))->toContain(50000);
        expect($response->json("data.supplier.id"))->toBe($this->supplier->id);
        expect($response->json("data.invoice_number"))->toBe('INV-001-UPDATED');
    });
    it("Should be success update payment status to paid", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $updatePayload = [
            'invoice_number' => 'INV-001-UPDATED',
            'status' => 'shipped',
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertOk();
        $response->assertJsonPath('data.invoice_number', 'INV-001-UPDATED');
        $response->assertJsonPath('data.status', 'shipped');

        expect($response->json("data.total_discount"))->toContain(50000);
        expect($response->json("data.supplier.id"))->toBe($this->supplier->id);
    });
    it("Should be success update payment status to unpaid", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $updatePayload = [
            'invoice_number' => 'INV-001-UPDATED',
            'status' => 'shipped',
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertOk();
        $response->assertJsonPath('data.invoice_number', 'INV-001-UPDATED');
        $response->assertJsonPath('data.status', 'shipped');

        expect($response->json("data.total_discount"))->toContain(50000);
        expect($response->json("data.supplier.id"))->toBe($this->supplier->id);
    });
    it("Should be success even tho payload is null",function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

       $response = $this->patchJson("/api/purchases/{$this->purchaseId}", []);

      $response->assertOk();
    });
    it("Should fail when updating purchase with duplicate invoice number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $duplicatePayload = $this->payload;
        $duplicatePayload['invoice_number'] = 'INV-001-DUPLICATE';
        $duplicatePayload['supplier_id'] = $this->newSupplier->id;
        $responseDuplicate = $this->postJson('/api/purchases', $duplicatePayload);
        $responseDuplicate->assertStatus(201);
        $duplicateId = $responseDuplicate->json('data.id');

        $updatePayload = [
            'invoice_number' => 'INV-001-DUPLICATE'
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertStatus(400);
        expect($response->json('errors.message.invoice_number.0'))->toBe('The invoice number has already been taken.');
    });
    it("Should be fail if id is not found", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

       $response = $this->patchJson("/api/purchases/999", []);
       $response->assertStatus(404);
    });
    it("Should be fail if id is not found even tho id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->patchJson("/api/purchases/X-INVALID-ID", []);
        $response->assertStatus(404);
    });
    it('Should increase product stock when status updated to delivered', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $initialStocks = $this->originalProducts->pluck('stock', 'id');

        $updatePayload = [
            'status' => 'delivered'
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'delivered');

        foreach ($this->originalProducts as $product) {
            $product->refresh();
            $expectedStock = $initialStocks[$product->id] + 5;
            expect($product->stock)->toBe($expectedStock);
        }
    });
    it('Should decrease product stock when status updated from delivered to other', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $initialStocks = $this->originalProducts->pluck('stock', 'id');

        $this->patchJson("/api/purchases/{$this->purchaseId}", [
            'status' => 'delivered'
        ])->assertOk();

        foreach ($this->originalProducts as $product) {
            $product->refresh();
            $expectedStock = $initialStocks[$product->id] + 5;
            expect($product->stock)->toBe($expectedStock);
        }

        $updatePayload = [
            'status' => 'shipped'
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertOk();
        $response->assertJsonPath('data.status', 'shipped');

        foreach ($this->originalProducts as $product) {
            $product->refresh();
            $expectedStock = $initialStocks[$product->id];
            expect($product->stock)->toBe($expectedStock);
        }
    });
    it('Should fail to revert delivered status if product stock insufficient', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $this->patchJson("/api/purchases/{$this->purchaseId}", [
            'status' => 'delivered'
        ])->assertOk();

        foreach ($this->originalProducts as $product) {
            $product->update(['stock' => 2]);
        }

        $updatePayload = [
            'status' => 'pending'
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatePayload);

        $response->assertStatus(422);
        expect($response->json('errors.message'))->toContain('Insufficient stock');
    });
    it("should update grand total after updating purchase details", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $originalPurchase = \App\Models\Purchase::with('details')->find($this->purchaseId);
        $originalGrandTotal = $originalPurchase->grand_total;

        $updatedDetails = [
            'purchase_details' => [
                [
                    'product_id' => $this->originalProducts[0]->id,
                    'quantity' => 6,
                ]
            ]
        ];

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}", $updatedDetails);
        $response->assertOk();

        $updatedPurchase = \App\Models\Purchase::with('details')->find($this->purchaseId);
        $updatedGrandTotal = $updatedPurchase->grand_total;

        $newTotalAmount = $updatedPurchase->details->sum(function ($detail) {
            return $detail->quantity * $detail->unit_price;
        });
        $expectedGrandTotal =  $newTotalAmount - $updatedPurchase->total_discount + $updatedPurchase->total_tax + $updatedPurchase->shipping_amount;
        $expectedGrandTotal2 = (String) round($expectedGrandTotal, 3);

        expect($updatedGrandTotal)->toBe($expectedGrandTotal2);
        expect($expectedGrandTotal2)->not->toBe($originalGrandTotal);
    });






});


describe("GET /api/purchases", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->newSupplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);
        $this->newProducts = $this->products->skip(3)->take(2);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);
        $this->newTax = $this->taxes->last();

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

    it("Should be success get all purchases", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases");
        $response->assertOk();
    });

    it("Should be success get all purchases with paging", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?page=2&per_page=5");
        $response->assertOk();
    });

    it("should filter purchases by invoice number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?invoice=INV-001");
        $response->assertOk();
        $response->assertJsonFragment(['invoice_number' => 'INV-001']);
    });

    it("should filter purchases by status", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?status=confirmed");
        $response->assertOk();
        $response->assertJsonFragment(['status' => 'confirmed']);
    });

    it("should filter purchases by payment status", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?payment_status=paid");
        dump($response->json());
        $response->assertOk();
        $response->assertJsonFragment(['payment_status' => 'paid']);
    });

    it("should filter purchases by supplier name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?supplier_name={$this->supplier->name}");
        $response->assertOk();
        expect($response->json('data.0.supplier.name'))->toBe($this->supplier->name);
    });

    it("should filter purchases by product name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $productName = $this->originalProducts->first()->name;
        $response = $this->getJson("/api/purchases?product_name={$productName}");
        $response->assertOk();
        $response->assertJsonFragment(['product_id' => $this->originalProducts->first()->id]);
    });

    it("should filter purchases by start date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $startDate = now()->subDay()->toDateString();
        $response = $this->getJson("/api/purchases?start_date={$startDate}");
        $response->assertOk();
        $response->assertJsonFragment(['id' => $this->purchaseId]);
    });

    it("should filter purchases by end date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $endDate = now()->addDay()->toDateString();
        $response = $this->getJson("/api/purchases?end_date={$endDate}");
        $response->assertOk();
        $response->assertJsonFragment(['id' => $this->purchaseId]);
    });

    it("should filter purchases by combined filters", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/purchases?invoice=INV-001&status=confirmed&payment_status=paid");
        $response->assertOk();
        $response->assertJsonFragment([
            'invoice_number' => 'INV-001',
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    });

});


describe("DELETE /api/purchases/{id}", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->newSupplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);
        $this->newProducts = $this->products->skip(3)->take(2);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);
        $this->newTax = $this->taxes->last();

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,

            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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

    it("should successfully soft delete purchase and cascade its relations", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson("/api/purchases/{$this->purchaseId}");

        $response->assertOk();

        $this->assertSoftDeleted('purchases', ['id' => $this->purchaseId]);
        foreach ($this->originalDetailIds as $id) {
            $this->assertSoftDeleted('purchase_details', ['id' => $id]);
        }
        foreach ($this->originalPaymentIds as $id) {
            $this->assertSoftDeleted('purchase_payments', ['id' => $id]);
        }

    });

});


describe("PATCH /api/purchases/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->newSupplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);
        $this->newProducts = $this->products->skip(3)->take(2);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);
        $this->newTax = $this->taxes->last();

        // Create the purchase payload
        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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

        // Create the purchase record
        $response = $this->actingAs($this->adminUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();
    });

    it("should successfully restore purchase and cascade its relations", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson("/api/purchases/{$this->purchaseId}");
        $response->assertOk();

        $this->assertSoftDeleted('purchases', ['id' => $this->purchaseId]);
        foreach ($this->originalDetailIds as $id) {
            $this->assertSoftDeleted('purchase_details', ['id' => $id]);
        }
        foreach ($this->originalPaymentIds as $id) {
            $this->assertSoftDeleted('purchase_payments', ['id' => $id]);
        }

        $response = $this->patchJson("/api/purchases/{$this->purchaseId}/restore");
        $response->assertOk();

        $this->assertDatabaseHas('purchases', [
            'id' => $this->purchaseId,
            'deleted_at' => null,
        ]);

        foreach ($this->originalDetailIds as $id) {
            $this->assertDatabaseHas('purchase_details', [
                'id' => $id,
                'deleted_at' => null,
            ]);
        }

        foreach ($this->originalPaymentIds as $id) {
            $this->assertDatabaseHas('purchase_payments', [
                'id' => $id,
                'deleted_at' => null,
            ]);
        }

        $this->assertDatabaseHas('purchase_taxes', [
            'purchase_id' => $this->purchaseId,
        ]);

    });
});

describe("GET /api/purchases/trashed", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->newSupplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);
        $this->newProducts = $this->products->skip(3)->take(2);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);
        $this->newTax = $this->taxes->last();

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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
    });

    it('Should be success get all trashed purchase', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        for ($i = 1; $i <= 50; $i++) {
            $payload = $this->payload;
            $payload['invoice_number'] = "INV-" . str_pad($i, 3, '0', STR_PAD_LEFT);

            $response = $this->postJson('/api/purchases', $payload);

            $purchaseId = $response->json('data.id');

            $this->deleteJson("/api/purchases/{$purchaseId}");
        }

        $response = $this->getJson('/api/purchases/trashed');
        expect($response->json('data'))->toHaveCount(10);
        $response->assertStatus(200);
    });

    it('Should be success get all trashed purchase with paging', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        for ($i = 1; $i <= 50; $i++) {
            $payload = $this->payload;
            $payload['invoice_number'] = "INV-" . str_pad($i, 3, '0', STR_PAD_LEFT);

            $response = $this->postJson('/api/purchases', $payload);

            $purchaseId = $response->json('data.id');

            $this->deleteJson("/api/purchases/{$purchaseId}");
        }

        $response = $this->getJson('/api/purchases/trashed?page=2&per_page=20');
        expect($response->json('data'))->toHaveCount(20);
        $response->assertStatus(200);
    });


    it('Should be success get all trashed purchase without untrashed purchase', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        for ($i = 1; $i <= 50; $i++) {
            $payload = $this->payload;
            $payload['invoice_number'] = "INV-" . str_pad($i, 3, '0', STR_PAD_LEFT);
            $this->postJson('/api/purchases', $payload);
        }

        $response = $this->getJson('/api/purchases/trashed');
        expect($response->json('data'))->toHaveCount(0);
        $response->assertStatus(200);
    });

});

describe("DELETE /api/purchases/{id}/force", function () {
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
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
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

    it('Should be success force delete purchase with their relation', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $this->postJson('/api/purchases', $this->payload);
        $this->deleteJson("/api/purchases/{$this->purchaseId}");

        $response = $this->deleteJson("/api/purchases/{$this->purchaseId}/force");
        $response->assertOk();
        $response->assertStatus(200);

        foreach ($this->originalDetailIds as $detailId) {
            $this->assertDatabaseMissing('purchase_details', ['id' => $detailId]);
        }

        foreach ($this->originalPaymentIds as $paymentId) {
            $this->assertDatabaseMissing('purchase_payments', ['id' => $paymentId]);
        }

        foreach ($this->originalTaxes as $tax) {
            $this->assertDatabaseMissing('purchase_taxes', [
                'purchase_id' => $this->purchaseId,
                'tax_id' => $tax->id,
            ]);
        }
    });

    it('Should fail force delete if purchase is not soft deleted first', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $this->postJson('/api/purchases', $this->payload);

        $response = $this->deleteJson("/api/purchases/{$this->purchaseId}/force");

        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                'message' => 'Cannot delete, purchase is not deleted'
            ]
        ]);

        $this->assertDatabaseHas('purchases', ['id' => $this->purchaseId]);
    });

    it('Should return 404 when trying to force delete non-existent purchase', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson("/api/purchases/999999/force");
        $response->assertStatus(404);
    });

    it('Should return 404 when trying to force delete non-existent purchase even id is invalid string', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson("/api/purchases/X-INVALID-ID/force");
        $response->assertStatus(404);
    });


});
