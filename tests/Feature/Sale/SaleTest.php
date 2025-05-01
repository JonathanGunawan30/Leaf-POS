<?php

use App\Models\Category;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/sales", function() {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        Sanctum::actingAs($this->adminUser);

        $this->tax = Tax::factory()->create([
            'rate' => 10,
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(3)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->customer = Customer::factory()->state([
            'address' => 'universitas buddhi dharma karawaci tangerang'
        ])->create();


        $this->courier = Courier::factory()->create();
    });

    it('can create a new sale', function () {
        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'pending',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('sales', [
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $this->products[0]->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('sale_payments', [
            'amount' => 500,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('shipments', [
            'vehicle_type' => 'car_van',
            'vehicle_number' => 'B1234XYZ',
        ]);
    });
    it('can create a new sale auto generate surat jalan', function () {
        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'confirmed',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('sales', [
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $this->products[0]->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('sale_payments', [
            'amount' => 500,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('shipments', [
            'vehicle_type' => 'car_van',
            'vehicle_number' => 'B1234XYZ',
        ]);
    });
    it('can create a new sale auto generate surat jalan dan invoice tempo', function () {
        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'delivered',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('sales', [
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $this->products[0]->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('sale_payments', [
            'amount' => 500,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('shipments', [
            'vehicle_type' => 'car_van',
            'vehicle_number' => 'B1234XYZ',
        ]);
    });
    it('can create a new sale auto generate surat jalan dan invoice', function () {
        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'delivered',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1000000,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);
        $response->assertCreated();

        $this->assertDatabaseHas('sales', [
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('sale_details', [
            'product_id' => $this->products[0]->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('sale_payments', [
            'amount' => 1000000,
            'payment_method' => 'cash',
        ]);

        $this->assertDatabaseHas('shipments', [
            'vehicle_type' => 'car_van',
            'vehicle_number' => 'B1234XYZ',
        ]);
    });
    it('decreases product stock after creating a sale', function () {
        $initialStock = $this->products[0]->stock;

        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 0,
            'status' => 'delivered',
            'customer_id' => $this->customer->id,
            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2, // Misal beli 2
                    'unit_price' => $this->products[0]->selling_price,
                ],
            ],
            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1000,
                    'payment_method' => 'cash',
                    'status' => 'paid',
                ],
            ],
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'id' => $this->products[0]->id,
            'stock' => $initialStock - 2, // Stock harus berkurang 2
        ]);
    });
    it('fails to create a sale if product stock is insufficient', function () {
        // Misal stock awal cuma 1
        $this->products[0]->update(['stock' => 1]);

        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 0,
            'status' => 'delivered',
            'customer_id' => $this->customer->id,
            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 5, // Minta beli 5, padahal stock cuma 1
                    'unit_price' => $this->products[0]->selling_price,
                ],
            ],
            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 5000,
                    'payment_method' => 'cash',
                    'status' => 'paid',
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            "errors" => [
                "message" => "Insufficient stock for product {$this->products[0]->name}."
            ]
        ]);
    });
    it('Should be fail because courier is unavailable', function () {
        $courier = Courier::factory(["status" => "unavailable"])->create();

        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'pending',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            "errors" => [
                "message" => "The courier is currently handling another delivery"
            ]
        ]);

    });
    it('Should be automatically updates courier status when shipment is created/delivered', function () {
        $courier = Courier::factory()->create();

        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'pending',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $sale = \App\Models\Sale::latest()->first();
        $shipment = $sale->shipments->first();

        $this->assertEquals('unavailable', $courier->fresh()->status);

        $shipment->update(['status' => 'delivered']);

        $this->assertEquals('available', $courier->fresh()->status);

    });
    it('Should be fail because courier is not found', function () {

        $response = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'pending',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 500,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'unpaid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => "x-notfound-kurir",
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'pending',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            "errors" => [
                "message" => "Courier not found"
            ]
        ]);

    });
});

describe("GET /api/sales/{id}", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        Sanctum::actingAs($this->adminUser);

        $this->tax = Tax::factory()->create([
            'rate' => 10,
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(3)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->customer = Customer::factory()->state([
            'address' => 'universitas buddhi dharma karawaci tangerang'
        ])->create();


        $this->courier = Courier::factory()->create();
    });

    it("Should be success get sale detail by id", function (){
        Sanctum::actingAs($this->adminUser);
        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'delivered',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1000000,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $saleId = $sale->json("data.id");

        $response = $this->getJson("/api/sales/{$saleId}");

        $response->assertOk();
    });

    it("Should return not found ", function (){
        Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/sales/9999");
        expect($response->json("errors.message"))->toBe("Sale not found");
        $response->assertStatus(404);
    });

    it("Should return not found even tho id is invalid string", function (){
        Sanctum::actingAs($this->adminUser);
        $response = $this->getJson("/api/sales/X-INVALID-ID");
        expect($response->json("errors.message"))->toBe("Sale not found");
        $response->assertStatus(404);
    });
});

describe("PATCH /api/sales/{id}", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(['name' => 'Admin']);
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        Sanctum::actingAs($this->adminUser);

        $this->tax = Tax::factory()->create([
            'rate' => 10,
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(3)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->customer = Customer::factory()->state([
            'address' => 'universitas buddhi dharma karawaci tangerang'
        ])->create();

        $this->customer2 = Customer::factory()->state([
            'address' => 'bsd tangerang'
        ])->create();


        $this->courier = Courier::factory()->create();
    });

    it("Should be success update sale detail by id and auto generate inv (the 1st input dsnt have inv num)", function (){
        Sanctum::actingAs($this->adminUser);
        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'confirmed',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1000000,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $saleId = $sale->json("data.id");

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'confirmed',
            'invoice_number' => null,
        ]);

        $response = $this->patchJson("/api/sales/{$saleId}", [
            'status' => 'delivered',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'delivered',
        ]);

        $updatedSale = \App\Models\Sale::find($saleId);
        $this->assertNotNull($updatedSale->invoice_number);
    });
    it("Should be success update sale detail by id and auto generate inv DP (the 1st input dsnt have inv DP num)", function (){
        Sanctum::actingAs($this->adminUser);
        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'confirmed',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $saleId = $sale->json("data.id");

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'confirmed',
            'invoice_downpayment_number' => null,
        ]);

        $response = $this->patchJson("/api/sales/{$saleId}", [
            'status' => 'delivered',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'delivered',
        ]);

        $updatedSale = \App\Models\Sale::find($saleId);
        $this->assertNotNull($updatedSale->invoice_downpayment_number);
    });
    it("Should be success update sale detail by id even tho payload is null", function (){
        Sanctum::actingAs($this->adminUser);
        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'confirmed',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $saleId = $sale->json("data.id");

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'confirmed',
            'invoice_downpayment_number' => null,
        ]);

        $response = $this->patchJson("/api/sales/{$saleId}", []);
        $response->assertStatus(200);

        $this->assertDatabaseHas('sales', [
            'id' => $saleId,
            'status' => 'confirmed',
        ]);

        $updatedSale = \App\Models\Sale::find($saleId);
        $this->assertNull($updatedSale->invoice_downpayment_number);
    });
    it("Should be success calculate shipping cost bc change customer", function (){
        Sanctum::actingAs($this->adminUser);
        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 100,
            'status' => 'confirmed',
            'due_date' => now()->addDays(7)->toDateString(),
            'note' => 'Test sale',
            'customer_id' => $this->customer->id,

            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail',
                ],
            ],

            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],

            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $saleId = $sale->json("data.id");
        $oldShippingCost = \App\Models\Sale::find($saleId)->shipments->first()?->shipping_cost ?? 0;
        dd($oldShippingCost);

        $response = $this->patchJson("/api/sales/{$saleId}", [
            'customer_id' => $this->customer2->id,
        ]);

        $response->assertStatus(200);

        $newShippingCost = \App\Models\Sale::find($saleId)->shipments->first()?->shipping_cost ?? 0;

        $this->assertNotEquals($oldShippingCost, $newShippingCost);

    });
    it("Should be success update and decrease product stock after updating a sale", function (){
        $initialStock = $this->products[0]->stock;
        $initialStock2 = $this->products[1]->stock;
        $sellingPrice = $this->products[0]->selling_price;
        $sellingPrice2 = $this->products[1]->selling_price;

        $sale = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 0,
            'status' => 'delivered',
            'customer_id' => $this->customer->id,
            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'note' => 'Test detail',
                ]
            ],
            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],
            'shipments' => [
                [
                    'courier_id' => $this->courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'delivered',
                    'note' => 'Test shipment',
                ],
            ],
        ]);

        $unitPrice = $sale->json('data.sale_details.0.unit_price');

        $this->assertEquals($unitPrice, $sellingPrice);

        $grandTotal = $sale->json('data.grand_total');

        $sale->assertCreated();

        $this->assertDatabaseHas('products', [
            'id' => $this->products[0]->id,
            'stock' => $initialStock - 2,
        ]);

        $update2 = $this->patchJson("/api/sales/{$sale->json('data.id')}", [
            'sale_details' => [
                [
                    'id' => $sale->json('data.sale_details.0.id'),
                    'product_id' => $this->products[1]->id,
                    'quantity' => 3,
                    'note' => 'Test detail',
                ]
            ],
        ]);

        $unitPrice2 = $update2->json('data.sale_details.0.unit_price');
        $this->assertEquals($unitPrice2, $sellingPrice2);

        $update2->assertOk();
        $this->assertDatabaseHas('products', [
            'id' => $this->products[1]->id,
            'stock' => $initialStock2 - 3,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->products[0]->id,
            'stock' => $initialStock,
        ]);

        $grandTotal2 = $update2->json('data.grand_total');

        $this->assertNotEquals($grandTotal, $grandTotal2);


    });
    it("Should update shipment status to delivered and makes courier available",  function (){
        $courier = $this->courier;

        expect($courier->status)->toBe('available');

        $saleResponse = $this->postJson('/api/sales', [
            'sale_date' => now()->toDateString(),
            'total_discount' => 0,
            'status' => 'delivered',
            'customer_id' => $this->customer->id,
            'sale_details' => [
                [
                    'product_id' => $this->products[0]->id,
                    'quantity' => 2,
                    'note' => 'Test detail',
                ]
            ],
            'sale_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 1,
                    'due_date' => now()->addDays(7)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => 'First payment',
                ],
            ],
            'shipments' => [
                [
                    'courier_id' => $courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'shipped',
                    'note' => 'Test shipment',
                ],
            ],
        ]);


        expect($saleResponse->json("data.shipments.0.courier.status"))->toBe('unavailable');


        $saleResponse2 = $this->patchJson("/api/sales/{$saleResponse->json('data.id')}", [
            'shipments' => [
                [
                    'id' => $saleResponse->json('data.shipments.0.id'), // Id shipment yang ingin diubah
                    'courier_id' => $courier->id, // Pastikan courier_id sesuai
                    'status' => 'delivered', // Status yang diupdate
                ]
            ],
        ]);


        $saleResponse2->assertOk();

        expect($saleResponse2->json("data.shipments.0.courier.status"))->toBe('available');




    });
});
