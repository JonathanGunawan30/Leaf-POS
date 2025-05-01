<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
describe("POST /api/units", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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
    });

    it("Should be successful create new unit", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $payLoad = [
            "name" => "kilo gram",
            "code" => "kg",
        ];
        $response = $this->postJson("/api/units", $payLoad);

        expect($response->status())->toBe(201);
        expect($response->json("message"))->toContain("success");
        expect($response->json("data.name"))->toContain("kilo gram");

    });

    it("Should be failed create new unit and return forbidden", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);
        $payLoad = [
            "name" => "kilo gram",
            "code" => "kg",
        ];
        $response = $this->postJson("/api/units", $payLoad);

        expect($response->status())->toBe(403);
        expect($response->json("errors.message"))->toContain("Forbidden");


    });

    it("Should be failed validation and return 400", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $payLoad = [
            "name" => "kilo gram",
            "code" => "kg",
        ];
        $response = $this->postJson("/api/units", $payLoad);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.name.0"))->toContain("already been taken.");
        expect($response->json("errors.message.code.0"))->toContain("already been taken.");


    });

    it("Should fail validation if required fields are missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [];

        $response = $this->postJson('/api/units', $payload);

        $response->assertStatus(400);
        expect($response->json('errors.message.name.0'))->toContain('required');
        expect($response->json('errors.message.code.0'))->toContain('required');
    });

});

describe("GET /api/units/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);
    });
   it("Should be successful get unit details", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $response = $this->getJson("/api/units/" . $this->unit->id);

       expect($response->status())->toBe(200);
       expect($response->json("data.name"))->toBe("kilo gram");
       expect($response->json("statusCode"))->toBe(200);
       expect($response->json("data.code"))->toBe("kg");
       expect($response->json("data.id"))->toBe($this->unit->id);
       expect($response->json("message"))->toContain("success");
   });

    it("Should be failed get unit details and return forbidden", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->getJson("/api/units/" . $this->unit->id);

        expect($response->status())->toBe(403);
        expect($response->json("errors.message"))->toBe("Forbidden");

    });

    it("Should be failed get unit details and return not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units/" . $this->unit->id * 99);

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");

    });

    it("Should be failed get unit details and return not found even tho id is invalid (string)", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units/X-INVALID-ID");

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");

    });
});

describe("PATCH /api/units/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->unit2 = \App\Models\Unit::create([
            "name" => "liter",
            "code" => "lt",
        ]);
    });

    it("Should be successful update unit details", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "liter UPDATED",
            "code" => "lt UPDATED",
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("data.name"))->toBe("liter UPDATED");
        expect($response->json("statusCode"))->toBe(200);
        expect($response->json("data.id"))->toBe($this->unit2->id);
        expect($response->json("message"))->toContain("success");
    });

    it("Should be failed update unit details and return 403", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $payLoad = [
            "name" => "liter UPDATED",
            "code" => "lt UPDATED",
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(403);
        expect($response->json("errors.message"))->toContain("Forbidden");

    });

    it("Should be failed update unit details and return 401", function () {

        $payLoad = [
            "name" => "liter UPDATED",
            "code" => "lt UPDATED",
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(401);
        expect($response->json("errors.message"))->toContain("Unauthenticated");

    });

    it("Should be successful update unit details even there is a null field", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "liter UPDATED",
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("data.name"))->toBe("liter UPDATED");
        expect($response->json("statusCode"))->toBe(200);
        expect($response->json("data.id"))->toBe($this->unit2->id);
        expect($response->json("message"))->toContain("success");
    });

    it("Should be successful update unit details even all field is null and return old data", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("data.name"))->toBe("liter");
        expect($response->json("statusCode"))->toBe(200);
        expect($response->json("data.id"))->toBe($this->unit2->id);
        expect($response->json("message"))->toContain("success");
    });

    it("Should be failed update unit details and return 400 invalid request", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $payLoad = [
            "name" => 3453452,
            "code" => 32354435,
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.code.0"))->toContain("The code field must be a string.");
        expect($response->json("errors.message.name.0"))->toContain("The name field must be a string.");

    });

    it("Should be successful update unit details even tho using old data", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "liter",
            "code" => "lt",
        ];

        $response = $this->patchJson("/api/units/" . $this->unit2->id, $payLoad);

        expect($response->status())->toBe(200);
        expect($response->json("data.name"))->toBe("liter");
        expect($response->json("statusCode"))->toBe(200);
        expect($response->json("data.id"))->toBe($this->unit2->id);
        expect($response->json("message"))->toContain("success");
    });
});

describe("GET /api/units", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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

        foreach (range(1, 30) as $i) {
            \App\Models\Unit::create([
                'name' => "Unit $i",
                'code' => "U$i",
            ]);
        }
    });

    it("should be successful get all units", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $response = $this->get("/api/units");
       expect($response->status())->toBe(200);

    });

    it("should return correct number of units per page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units?page=2&per_page=5");

        $response->assertStatus(200);

        expect($response->json("data"))->toHaveCount(5);
        expect($response->json("meta.current_page"))->toBe(2);
        expect($response->json("meta.per_page"))->toBe(5);
    });

    it("should return default 10 units per page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units");

        $response->assertStatus(200);
        expect($response->json("data"))->toHaveCount(10);
        expect($response->json("meta.per_page"))->toBe(10);
    });

    it("should contain meta and links in the response", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units");

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(['meta', 'links']);
        expect($response->json("meta"))->toHaveKeys(['current_page', 'from', 'last_page', 'per_page', 'to', 'total']);
    });
    it("should return empty data on high page number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/units?page=999");

        $response->assertStatus(200);
        expect($response->json("data"))->toHaveCount(0);
    });

});

describe("DELETE /api/units", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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
    });
    it("Should be successful delete units", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/units/" . $this->unit->id);

        expect($response->status())->toBe(200);
        expect($response->json("data.message"))->toContain("success");

        $this->assertSoftDeleted('units', [
            'id' => $this->unit->id
        ]);
    });

    it("Should fail to delete unit if related to product", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

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


        $response = $this->delete("/api/units/" . $this->unit->id);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message"))->toContain("Cannot delete unit because it has related products");
    });
    it("Should fail to delete non-existent unit", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/units/99999");

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toBe("Unit not found");
    });

    it("Should fail to delete unit without authorization", function () {

        $response = $this->deleteJson("/api/units/" . $this->unit->id);

        expect($response->status())->toBe(401);
    });



});

describe("PATCH /api/units/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

    });

    it("should restore deleted unit", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->unit->delete();

        $response = $this->patchJson("/api/units/{$this->unit->id}/restore");

        $response->assertStatus(200);
        expect($response->json('data.message'))->toBe('Unit restored successfully');

        $this->assertDatabaseHas('units', [
            'id' => $this->unit->id,
            'deleted_at' => null,
        ]);
    });

    it("should fail to restore if unit not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/units/9999/restore");

        $response->assertStatus(404);
        expect($response->json('errors.message'))->toBe('Unit not found');
    });


    it("should fail to restore if unit not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/units/X-INVALID-ID/restore");

        $response->assertStatus(404);
        expect($response->json('errors.message'))->toBe('Unit not found');
    });

    it("should fail to restore if unit is not deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/units/{$this->unit->id}/restore");

        $response->assertStatus(400);
        expect($response->json('errors.message'))->toBe('Unit is not deleted');
    });

});

describe("DELETE /api/units/{id}/force", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->inventoryRole = \App\Models\Role::query()->create([
            "name" => "Inventory"
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
    });
    it("should successfully force delete soft-deleted unit", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->unit->delete();

        $response = $this->delete("/api/units/{$this->unit->id}/force");

        expect($response->status())->toBe(200);
        expect($response->json("data.message"))->toContain("success");

        $this->assertDatabaseMissing('units', [
            'id' => $this->unit->id
        ]);
    });

    it("should fail to force delete if unit is not soft-deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/units/{$this->unit->id}/force");

        expect($response->status())->toBe(400);
        expect($response->json("errors.message"))->toContain("Unit is not deleted");

        $this->assertDatabaseHas('units', [
            'id' => $this->unit->id
        ]);
    });

    it("should fail to force delete if unit has related products", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

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

        $this->unit->delete();

        $response = $this->delete("/api/units/{$this->unit->id}/force");

        expect($response->status())->toBe(400);
        expect($response->json("errors.message"))->toContain("Cannot force delete unit because it has related products");

        $this->assertSoftDeleted('units', [
            'id' => $this->unit->id
        ]);
    });

    it("should fail to force delete if unit ID (integer) not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/units/999999/force");

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("Unit not found");
    });

    it("should fail to force delete if unit ID is a string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/units/invalid-id/force");

        expect($response->status())->toBe(404);
        expect($response->json("errors.message"))->toContain("Unit not found");
    });

    it("should fail to force delete if unauthenticated", function () {
        $response = $this->deleteJson("/api/units/{$this->unit->id}/force");

        expect($response->status())->toBe(401);
    });

    it("Should successfully force delete after restore and soft delete again", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->unit->delete();
        expect($this->unit->fresh()->trashed())->toBeTrue();

        $this->unit->restore();
        expect($this->unit->fresh()->trashed())->toBeFalse();

        $this->unit->delete();
        expect($this->unit->fresh()->trashed())->toBeTrue();

        $response = $this->delete("/api/units/{$this->unit->id}/force");

        $response->assertStatus(200);
        expect(\App\Models\Unit::withTrashed()->find($this->unit->id))->toBeNull();
    });

});
