<?php

use App\Models\Tax;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/taxes", function() {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);
    });

   it("Should be successful create new tax [admin]", function() {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $payLoad = [
         "name" => "PPN",
         "rate" => 11
       ];

       $response = $this->post("/api/taxes", $payLoad);

       expect($response->status())->toBe(201);
       expect($response->json("data.name"))->toContain("PPN");
       expect($response->json("message"))->toContain("success");
   });

    it("Should be successful create new tax [finance]", function() {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $payLoad = [
            "name" => "PPN",
            "rate" => 11
        ];

        $response = $this->post("/api/taxes", $payLoad);

        expect($response->status())->toBe(201);
        expect($response->json("data.name"))->toContain("PPN");
        expect($response->json("message"))->toContain("success");
    });

    it("Should be failed create new tax and return forbidden[inventory]", function() {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $payLoad = [
            "name" => "PPN",
            "rate" => 11
        ];

        $response = $this->post("/api/taxes", $payLoad);

        expect($response->status())->toBe(403);
        expect($response->json("errors.message"))->toContain("Forbidden");
    });

    it("Should be failed create new tax if the name already exists", function() {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "PPN",
            "rate" => 11
        ];

        $this->post("/api/taxes", $payLoad);
        $response = $this->post("/api/taxes", $payLoad);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.name.0"))->toContain("The name has already been taken.");
    });

    it("Should fail to create tax if unauthenticated", function () {
        $response = $this->postJson("/api/taxes", [
            "name" => "PPN",
            "rate" => 11
        ]);

        expect($response->status())->toBe(401);
        expect($response->json("errors.message"))->toContain("Unauthenticated");
    });

    it("Should fail to create tax if rate is negative", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "name" => "Negative Tax",
            "rate" => -5
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.rate.0"))->toContain("The rate field must be between 0 and 999.99.");
    });
    it("Should fail to create tax if rate exceeds max value", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "name" => "Overflow Tax",
            "rate" => 1000
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.rate.0"))->toContain("The rate field must be between 0 and 999.99.");
    });

    it("Should fail to create tax if name is missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "rate" => 10
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.name.0"))->toContain("The name field is required.");
    });

    it("Should fail to create tax if rate is missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "name" => "No Rate"
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.rate.0"))->toContain("The rate field is required.");
    });
    it("Should fail to create tax if rate is string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "name" => "Invalid Rate",
            "rate" => "ten percent"
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.rate.0"))->toContain("The rate field must be a number.");
    });

    it("Should fail to create tax if name exceeds 100 characters", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/taxes", [
            "name" => str_repeat("a", 101),
            "rate" => 10
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.name.0"))->toContain("The name field must not be greater than 100 characters.");
    });

});



describe("GET /api/taxes/{id}", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->tax = \App\Models\Tax::query()->create([
            "name" => "PPN",
            "rate" => 11
        ]);
    });

    it("Should be successful get tax by id ", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->getJson("/api/taxes/" . $this->tax->id);

        expect($response->status())->toBe(200);
        expect($response->json("message"))->toContain("success");
    });

    it("Should be failed get tax by id and return not found ", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->getJson("/api/taxes/" . $this->tax->id * 10);

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should be failed get tax by id and return not found even tho id is invalid string ", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->getJson("/api/taxes/X-INVALID-ID");

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");
    });
});

describe("PATCH /api/taxes/{id} ", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->tax = \App\Models\Tax::query()->create([
            "name" => "PPN",
            "rate" => 11
        ]);
    });

    it("Should be successful update tax by id ", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $payLoad = [
           "name" => "PPN UPDATED",
           "rate" => 12
       ];

       $response = $this->patchJson("/api/taxes/" . $this->tax->id, $payLoad);

       expect($response->status())->toBe(200);
       expect($response->json("message"))->toBe("Tax updated successfully");
       expect($response->json("data.name"))->toBe("PPN UPDATED");

    });

    it("Should be successful update tax by id only name field", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "PPN UPDATED"
        ];

        $response = $this->patchJson("/api/taxes/" . $this->tax->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("message"))->toBe("Tax updated successfully");
        expect($response->json("data.name"))->toBe("PPN UPDATED");

    });

    it("Should be successful update tax by id only rate field", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "rate" => 12
        ];

        $response = $this->patchJson("/api/taxes/" . $this->tax->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("message"))->toBe("Tax updated successfully");
        expect($response->json("data.rate"))->toBe(12);

    });

    it("Should be successful update tax by id even tho field is null", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [];

        $response = $this->patchJson("/api/taxes/" . $this->tax->id, $payLoad);


        expect($response->status())->toBe(200);
        expect($response->json("message"))->toBe("Tax updated successfully");

    });

    it("Should be failed update tax by id and return already been taken", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Tax::query()->create([
            "name" => "PPN UPDATED",
            "rate" => 12
        ]);

        $payLoad = [
            "name" => "PPN UPDATED",
            "rate" => 12
        ];

        $response = $this->patchJson("/api/taxes/" . $this->tax->id, $payLoad);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.name.0"))->toContain("already been taken");

    });

    it("Should be failed update tax by id and return not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "PPN UPDATED",
            "rate" => 12
        ];

        $response = $this->patchJson("/api/taxes/" . $this->tax->id * 10, $payLoad);

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toBe("Tax not found");

    });

    it("Should be failed update tax by id and return not found even tho invalid id string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "PPN UPDATED",
            "rate" => 12
        ];

        $response = $this->patchJson("/api/taxes/X-INVALID-ID", $payLoad);

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toBe("Tax not found");

    });

});

describe("GET /api/taxes", function (){
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        for ($i = 1; $i <= 50; $i++) {
            Tax::create([
                'name' => 'Tax ' . $i,
                'rate' => rand(1, 25) + (rand(0, 99) / 100),
            ]);
        }
    });

    it("Should be successful get all tax", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/taxes");

        expect($response->status())->toBe(200);
        expect($response->json("message"))->toBe("Taxes retrieved successfully");

    });

    it("Should return paginated taxes with correct meta structure", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);


        $response = $this->getJson("/api/taxes?page=1&per_page=10");

        $response->assertStatus(200);
        $json = $response->json();

        expect($json)->toHaveKeys(['data', 'message', 'statusCode', 'meta']);
        expect($json['meta'])->toHaveKeys(['current_page', 'per_page', 'total', 'last_page', 'from', 'to', 'links']);
        expect(count($json['data']))->toBe(10);
        expect($json['meta']['current_page'])->toBe(1);
        expect($json['meta']['per_page'])->toBe(10);
        expect($json['meta']['total'])->toBeGreaterThanOrEqual(50);
    });

    it("Should return correct paginated taxes on multiple pages", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response1 = $this->getJson("/api/taxes?page=1&per_page=10");
        $json1 = $response1->json();

        expect($response1->status())->toBe(200);
        expect(count($json1['data']))->toBe(10);
        expect($json1['meta']['current_page'])->toBe(1);
        expect($json1['meta']['per_page'])->toBe(10);

        $response2 = $this->getJson("/api/taxes?page=2&per_page=10");
        $json2 = $response2->json();

        expect($response2->status())->toBe(200);
        expect(count($json2['data']))->toBe(10);
        expect($json2['meta']['current_page'])->toBe(2);
        expect($json2['meta']['per_page'])->toBe(10);

        $response3 = $this->getJson("/api/taxes?page=3&per_page=20");
        $json3 = $response3->json();

        expect($response3->status())->toBe(200);

        expect($json3['meta']['current_page'])->toBe(3);
        expect($json3['meta']['per_page'])->toBe(20);
        expect(count($json3['data']))->toBe(10);

        $response4 = $this->getJson("/api/taxes?page=2&per_page=25");
        $json4 = $response4->json();

        expect($response4->status())->toBe(200);
        expect($json4['meta']['current_page'])->toBe(2);
        expect($json4['meta']['per_page'])->toBe(25);
        expect(count($json4['data']))->toBe(25);

        foreach ([$json1, $json2, $json3, $json4] as $json) {
            expect($json)->toHaveKeys(['data', 'message', 'statusCode', 'meta']);
            expect($json['meta'])->toHaveKeys([
                'current_page', 'per_page', 'total', 'last_page', 'from', 'to', 'links'
            ]);
        }
    });


});

describe("DELETE /api/taxes/{id}", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->tax = \App\Models\Tax::query()->create([
            "name" => "PPN",
            "rate" => 11
        ]);
        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->supplier = \App\Models\Supplier::create([
            'name' => 'Sample Supplier',
            'company_name' => 'Supplier Corp',
            'email' => 'supplier@example.com',
            'phone' => '08123456789',
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'address' => 'Jl. Contoh No. 1',
            'bank_account' => '1234567890',
            'bank_name' => 'Bank Contoh',
            'npwp_number' => '09.123.456.7-890.000',
            'siup_number' => 'SIUP123456',
            'nib_number' => 'NIB987654',
            'business_type' => 'Retail',
            'note' => 'Test supplier for unit testing'
        ]);

        $this->product = \App\Models\Product::create([
            'name' => 'Sample Product',
            'sku' => 'SP001',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'stock' => 10,
            'stock_alert' => 2,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'description' => 'Sample description',
            'brand' => 'Brand X',
            'discount' => 0,
            'weight' => 1.5,
            'volume' => 0.75,
            'barcode' => '1234567890123',
            'images' => null,
        ]);
    });
    it("Should be success delete tax by id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);


        $response = $this->deleteJson("/api/taxes/" . $this->tax->id);

        $response->assertStatus(200);
        expect($response->json("data.message"))->toBe("Tax deleted successfully");

        $this->assertSoftDeleted('taxes', ['id' => $this->tax->id]);
    });

    it("Should fail to delete tax if related to product via pivot", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $tax = $this->tax;

        $product = \App\Models\Product::first();

        $tax->products()->attach($product->id, ['amount' => 10]);

        $response = $this->deleteJson("/api/taxes/{$tax->id}");

        $response->assertStatus(400);
        expect($response->json('errors.message'))
            ->toBe("Tax cannot be deleted because it is associated with products");

        $this->assertDatabaseHas('taxes', [
            'id' => $tax->id,
            'deleted_at' => null
        ]);
    });

    it("Should be failed delete tax by id and return not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);


        $response = $this->deleteJson("/api/taxes/234523452233223");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Tax not found");

    });

    it("Should be failed delete tax by id and return not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);


        $response = $this->deleteJson("/api/taxes/X-INVALID-ID");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Tax not found");

    });

    it("Should be failed delete tax by id because there is a relationship", function () {
        $payload = [
            'invoice_number' => 'INV-TEST',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 100000,
            'total_discount' => 0,
            'shipping_amount' => 0,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 100000,
                    'sub_total' => 100000,
                    'note' => ''
                ]
            ],
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => ''
                ]
            ],
            'taxes' => [
                [
                    'tax_id' => $this->tax->id
                ]
            ]
        ];

        $this->actingAs($this->admin)->postJson('/api/purchases', $payload);

        $response = $this->actingAs($this->admin)->deleteJson("/api/taxes/{$this->tax->id}");

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => 'Tax cannot be deleted because it is associated with purchases'
            ]
        ]);
    });

});


describe("PATCH /api/taxes/{id}/restore", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->tax = \App\Models\Tax::query()->create([
            "name" => "PPN",
            "rate" => 11
        ]);
        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->supplier = \App\Models\Supplier::create([
            'name' => 'Sample Supplier',
            'company_name' => 'Supplier Corp',
            'email' => 'supplier@example.com',
            'phone' => '08123456789',
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'address' => 'Jl. Contoh No. 1',
            'bank_account' => '1234567890',
            'bank_name' => 'Bank Contoh',
            'npwp_number' => '09.123.456.7-890.000',
            'siup_number' => 'SIUP123456',
            'nib_number' => 'NIB987654',
            'business_type' => 'Retail',
            'note' => 'Test supplier for unit testing'
        ]);

        \App\Models\Product::create([
            'name' => 'Sample Product',
            'sku' => 'SP001',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'stock' => 10,
            'stock_alert' => 2,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'description' => 'Sample description',
            'brand' => 'Brand X',
            'discount' => 0,
            'weight' => 1.5,
            'volume' => 0.75,
            'barcode' => '1234567890123',
            'images' => null,
        ]);
    });

    it("Should be Success restore tax data", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->tax->delete();

        $response = $this->patchJson("/api/taxes/". $this->tax->id . "/restore");
        $response->assertStatus(200);
        expect($response->json("data")["name"])->toBe("PPN");
        expect($response->json("message"))->toBe("Tax restored successfully");


    });

    it("Should be Failed restore tax data if data is not deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/taxes/". $this->tax->id . "/restore");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toContain("Cannot restore, tax has not been soft deleted.");
    });
});


describe("DELETE /api/taxes/{id}/force", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->tax = \App\Models\Tax::query()->create([
            "name" => "PPN",
            "rate" => 11
        ]);
        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->supplier = \App\Models\Supplier::create([
            'name' => 'Sample Supplier',
            'company_name' => 'Supplier Corp',
            'email' => 'supplier@example.com',
            'phone' => '08123456789',
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'address' => 'Jl. Contoh No. 1',
            'bank_account' => '1234567890',
            'bank_name' => 'Bank Contoh',
            'npwp_number' => '09.123.456.7-890.000',
            'siup_number' => 'SIUP123456',
            'nib_number' => 'NIB987654',
            'business_type' => 'Retail',
            'note' => 'Test supplier for unit testing'
        ]);

        $this->product = \App\Models\Product::create([
            'name' => 'Sample Product',
            'sku' => 'SP001',
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'stock' => 10,
            'stock_alert' => 2,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'description' => 'Sample description',
            'brand' => 'Brand X',
            'discount' => 0,
            'weight' => 1.5,
            'volume' => 0.75,
            'barcode' => '1234567890123',
            'images' => null,
        ]);
    });

    it("Should be Success delete tax data", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $this->tax->delete();

       $response = $this->delete("/api/taxes/". $this->tax->id . "/force");
       $response->assertStatus(200);
       expect($response->json())->toContain("Tax deleted successfully");
    });

    it("Should be failed delete tax data and return tax is not deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/taxes/". $this->tax->id . "/force");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toContain("Cannot hard delete, tax has not been soft deleted.");
    });

    it("Should be failed delete tax data and return tax is still has relation", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->product->taxes()->attach($this->tax->id, ['amount' => 1000]);
        $this->tax->delete();
        $response = $this->delete("/api/taxes/". $this->tax->id . "/force");
        $response->assertStatus(400);
        expect($response->json("errors.message"))->toContain("Cannot hard delete, tax is still related to one or more products.");
    });

    it("Should be failed delete tax by id because there is a relationship", function () {
        $payload = [
            'invoice_number' => 'INV-TEST',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 100000,
            'total_discount' => 0,
            'shipping_amount' => 0,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'unit_price' => 100000,
                    'sub_total' => 100000,
                    'note' => ''
                ]
            ],
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 100000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'cash',
                    'status' => 'paid',
                    'note' => ''
                ]
            ],
            'taxes' => [
                [
                    'tax_id' => $this->tax->id
                ]
            ]
        ];

        $this->actingAs($this->admin)->postJson('/api/purchases', $payload);

        $this->tax->delete();
        $response = $this->actingAs($this->admin)->deleteJson("/api/taxes/{$this->tax->id}/force");


        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => 'Cannot hard delete, tax is still related to one or more purchases.'
            ]
        ]);
    });
});
