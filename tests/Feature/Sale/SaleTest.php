<?php

use App\Models\Category;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
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
    it("Should be added new shipment if the first create have no shipment",  function (){
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
        ]);



        $saleResponse2 = $this->patchJson("/api/sales/{$saleResponse->json('data.id')}", [
            'shipments' => [
                [
                    'courier_id' => $courier->id,
                    'vehicle_type' => 'car_van',
                    'vehicle_number' => 'B1234XYZ',
                    'shipping_date' => now()->toDateString(),
                    'estimated_arrival_date' => now()->addDays(1)->toDateString(),
                    'status' => 'shipped',
                    'note' => 'Test shipment',
                ]
            ],
        ]);

        $saleResponse2->assertOk();

        expect($saleResponse2->json("data.shipments.0.courier.status"))->toBe('unavailable');

    });
    it("Should be success update sale detail even tho id is not given", function (){
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $response = $this->patchJson("/api/sales/{$saleId}", [
            'sale_details' => [
                [
                    "id" => $sale->json("data.sale_details.0.id"),
                    "note" => "TEST DETAIL UPDATED",
                ]
            ]
        ]);

        $response->assertStatus(200);
    });


    // ID SELALU REQUIRED UNTUK UPDATE, JIKA DATA YANG LAIN TIDAK DIKIRIM, MAKA AKAN MENGGUNAKAN DATA YANG LAMA (STANDARD UDPATE)
    // LALU GIMANA UNTUK MENGHAPUS DETAIL ATAUPUN MENAMBAHKAN DETAIL? PERLU ENDPOINT TAMBAHAN


});

describe("POST /api/sales/{id}/details", function () {
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

    it("Should be success create sale detail", function (){
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $response = $this->postJson("/api/sales/{$sale->json('data.id')}/details", [
            'product_id' => $this->products[2]->id,
            'quantity' => 1,
            'note' => 'Test detail 3',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('sale_details', [
            'product_id' => $this->products[2]->id,
            'quantity' => 1,
            'unit_price' => $this->products[2]->selling_price,
        ]);
        $this->assertCount(3, \App\Models\Sale::find($sale->json('data.id'))->details);


    });
    it("Should fail create sale detail when product not found", function () {
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $response = $this->postJson("/api/sales/{$sale->json('data.id')}/details", [
            'product_id' => 999999,
            'quantity' => 1,
            'note' => 'Invalid product',
        ]);

        $response->assertStatus(400)
            ->assertJson([
               "errors" => [
                   "message" => [
                       "product_id" => [
                           "The selected product id is invalid."
                       ]
                   ]
               ]
            ]);
    });
    it("Should fail create sale detail when stock not enough", function () {
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $product = $this->products[0];
        $product->update(['stock' => 1]);

        $response = $this->postJson("/api/sales/{$sale->json('data.id')}/details", [
            'product_id' => $product->id,
            'quantity' => 999,
            'note' => 'Too much',
        ]);

        $response->assertStatus(422);
        expect($response->json("errors.message"))->toContain("Insufficient stock for product");
    });
    it("Should fail create sale detail when sale id not found", function () {
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $product = $this->products[0];
        $product->update(['stock' => 1]);

        $response = $this->postJson("/api/sales/999999/details", [
            'product_id' => $product->id,
            'quantity' => 999,
            'note' => 'Too much',
        ]);

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toContain("Sale id not found");
    });
    it("Should fail create sale detail when sale id not found even tho id is invalid string", function () {
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ]
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

        $product = $this->products[0];
        $product->update(['stock' => 1]);

        $response = $this->postJson("/api/sales/x-9invalid-id/details", [
            'product_id' => $product->id,
            'quantity' => 999,
            'note' => 'Too much',
        ]);

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toContain("Sale id not found");
    });

});

describe("DELETE /api/sales/{saleId}/details/{saleDetailId}", function () {
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

        $this->sale = $this->postJson('/api/sales', [
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
                [
                    'product_id' => $this->products[1]->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                    'note' => 'Test detail 2',
                ],
                [
                    'product_id' => $this->products[2]->id,
                    'quantity' => 1,
                    'unit_price' => 1500,
                    'note' => 'Test detail 3',
                ]
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
    });

    it('should successfully delete a sale detail and leave 2 remaining', function () {
        $saleId = $this->sale->json('data.id');
        $detailId = Sale::find($saleId)->details()->latest()->first()->id; // ambil 1 detail untuk dihapus

        $response = $this->deleteJson("/api/sales/{$saleId}/details/{$detailId}");
        $response->assertOk()->assertJson([
            "data"=> [
                'message' => 'Sale detail deleted successfully'
            ]
        ]);

        $this->assertDatabaseMissing('sale_details', ['id' => $detailId]);

        $this->assertCount(2, Sale::find($saleId)->details);
    });
    it('should return 404 when sale not found', function () {
        $invalidSaleId = 999999;
        $validDetailId = SaleDetail::first()->id;

        $response = $this->deleteJson("/api/sales/{$invalidSaleId}/details/{$validDetailId}");
        $response->assertNotFound();
    });
    it('should return 404 when sale detail not found in the sale', function () {
        $saleId = $this->sale->json('data.id');
        $invalidDetailId = 999999;

        $response = $this->deleteJson("/api/sales/{$saleId}/details/{$invalidDetailId}");
        $response->assertNotFound();
    });
    it('should return 404 when both sale and sale detail not found', function () {
        $invalidSaleId = 999999;
        $invalidDetailId = 888888;

        $response = $this->deleteJson("/api/sales/{$invalidSaleId}/details/{$invalidDetailId}");
        $response->assertNotFound();
    });
});

describe("GET /api/sales", function (){
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

        Sale::factory()->count(5)
            ->confirmed()
            ->withDetails()
            ->withPayments()
            ->withShipments()
            ->create();


    });
    it("Should be success get all sales", function () {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/sales');

        $response->assertStatus(200);

        $response->assertJsonCount(5, 'data');

    });
});

describe("DELETE /api/sales/{id}", function (){
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

        $this->sale = Sale::factory()->count(5)
            ->confirmed()
            ->withDetails()
            ->withPayments()
            ->withShipments()
            ->create();
    });

    it("Should be success soft delete sales by id", function (){
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson("/api/sales/{$this->sale->first()->id}");
        expect($response->json("data.message"))->toBe("Sale deleted successfully");
    });

    it("Should soft delete sale and delete related details, payments, and shipments", function () {
        Sale::query()->delete();

        $sale = Sale::factory()
            ->confirmed()
            ->withDetails(1)
            ->withPayments(1)
            ->withShipments(1)
            ->createOne([
                'customer_id' => $this->customer->id,
            ]);

        $saleId = $sale->id;

        $this->assertEquals(1, $sale->details()->count());
        $this->assertEquals(1, $sale->payments()->count());
        $this->assertEquals(1, $sale->shipments()->count());

        $response = $this->deleteJson("/api/sales/{$saleId}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => ['message' => 'Sale deleted successfully']
        ]);

        $this->assertSoftDeleted('sales', ['id' => $saleId]);

        $this->assertDatabaseHas('sale_details', ['sale_id' => $saleId]);
        $this->assertDatabaseHas('sale_payments', ['sale_id' => $saleId]);
        $this->assertDatabaseHas('shipments', ['sale_id' => $saleId]);

        $this->assertSoftDeleted('sale_details', ['sale_id' => $saleId]);
        $this->assertSoftDeleted('sale_payments', ['sale_id' => $saleId]);
        $this->assertSoftDeleted('shipments', ['sale_id' => $saleId]);

    });
});

describe("PATCH /api/sales/{id}/restore", function (){
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
    it("Should be success restore sales by id", function (){
        Sanctum::actingAs($this->adminUser);

        $sale = Sale::factory()
            ->confirmed()
            ->withDetails(1)
            ->withPayments(1)
            ->withShipments(1)
            ->createOne([
                'customer_id' => $this->customer->id,
            ]);

        $saleId = $sale->id;

        $sale->delete();

        $response = $this->patchJson("/api/sales/{$saleId}/restore");
        expect($response->json("message"))->toBe("Sale restored successfully");

    });

    it("Should be success restore sales by id with its children", function () {
        Sanctum::actingAs($this->adminUser);

        $sale = Sale::factory()
            ->confirmed()
            ->withDetails(1)
            ->withPayments(1)
            ->withShipments(1)
            ->createOne([
                'customer_id' => $this->customer->id,
            ]);

        $saleId = $sale->id;

        $detailId = $sale->details->first()->id;
        $paymentId = $sale->payments->first()->id;
        $shipmentId = $sale->shipments->first()->id;
        $sale->delete();

        $this->assertSoftDeleted('sale_details', ['id' => $detailId]);
        $this->assertSoftDeleted('sale_payments', ['id' => $paymentId]);
        $this->assertSoftDeleted('shipments', ['id' => $shipmentId]);

        $response = $this->patchJson("/api/sales/{$saleId}/restore");
        $response->assertStatus(200);
        expect($response->json("message"))->toBe("Sale restored successfully");

        $this->assertDatabaseHas('sale_details', [
            'id' => $detailId,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('sale_payments', [
            'id' => $paymentId,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('shipments', [
            'id' => $shipmentId,
            'deleted_at' => null,
        ]);
    });

});

describe("GET /api/sales/trashed", function (){
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

        Sale::factory()->count(5)
            ->confirmed()
            ->withDetails()
            ->withPayments()
            ->withShipments()
            ->create();

    });
    it("Should return trashed sales with default pagination", function () {

        $trashedSales = Sale::take(3)->get();
        foreach ($trashedSales as $sale) {
            $sale->delete();
        }

        $response = $this->getJson('/api/sales/trashed');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
        $response->assertJsonCount(3, 'data');
    });

    it("Should return paginated trashed sales with per_page query", function () {
        Sale::all()->each->delete();

        $response = $this->getJson('/api/sales/trashed?per_page=2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total']
        ]);
        $response->assertJsonCount(2, 'data');
    });

});

describe("DELETE /api/sales/{id}/force", function () {
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
    it("Should force delete sale and related data", function () {
        Sanctum::actingAs($this->adminUser);

        $sale = Sale::factory()
            ->confirmed()
            ->withDetails(1)
            ->withPayments(1)
            ->withShipments(1)
            ->createOne([
                'customer_id' => $this->customer->id,
            ]);

        $sale->delete();

        $response = $this->deleteJson("/api/sales/{$sale->id}/force");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => ['message' => 'Sale deleted successfully']
        ]);

        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseMissing('sale_details', ['sale_id' => $sale->id]);
        $this->assertDatabaseMissing('sale_payments', ['sale_id' => $sale->id]);
        $this->assertDatabaseMissing('shipments', ['sale_id' => $sale->id]);
    });

});

describe("GET /api/sale-reports", function () {
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
        $this->sale = Sale::factory()
            ->confirmed()
            ->withDetails(1)
            ->withPayments(1)
            ->withShipments(1)
            ->count(10)
            ->create();
    });

    it("Should return sale report excel", function () {
        $response = $this->getJson('/api/sale-reports');

        $response->assertStatus(200);
    });

    it("Should return sale report pdf", function () {
        $response = $this->getJson('/api/sale-reports?format=pdf');

        $response->assertStatus(200);
    });


});

describe("GET /api/sales/{id}/delivery-note", function () {
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
        $this->sale = Sale::factory()
            ->confirmed()
            ->withDetails(10)
            ->withPayments(1)
            ->withShipments(1)
            ->count(1)
            ->create();
    });

    it("Should return delivery note pdf", function () {
        $response = $this->getJson("/api/sales/{$this->sale->first()->id}/delivery-note");
        $response->assertStatus(200);

    });
});


