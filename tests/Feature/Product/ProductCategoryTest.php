<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
describe("POST /api/categories", function (){
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
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

        $this->purchasing = \App\Models\User::query()->create([
            "name" => "purchasing",
            "email" => "purchasing@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);
    });

    it("Should be success create category product [admin]", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $response = $this->postJson("/api/categories", ["name" => "Electronics"]);

       $response->assertStatus(201);
       expect($response->json("message"))->toBe("Category created successfully");

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    });

    it("Should be success create category product [purchasing]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->purchasing);

        $response = $this->postJson("/api/categories", ["name" => "Electronics"]);

        $response->assertStatus(201);
        expect($response->json("message"))->toBe("Category created successfully");

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics'
        ]);
    });

    it("Should be failed create category product and return 403 forbidden", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->postJson("/api/categories", ["name" => "Electronics"]);

        $response->assertStatus(403);
        expect($response->json("errors.message"))->toBe("Forbidden");
    });

    it("Should fail to create category if name is not unique", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Category::create(['name' => 'Electronics']);

        $response = $this->postJson("/api/categories", ['name' => 'Electronics']);

        $response->assertStatus(400);
        expect($response->json("errors.message.name.0"))->toBe("The name has already been taken.");
    });

    it("Should fail to create category if payload is empty", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->postJson("/api/categories", []);

        $response->assertStatus(400);
        expect($response->json("errors.message.name.0"))->toContain("required");
    });


});
describe("GET /api/categories/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => bcrypt("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be success retrieve category by id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "Gadget"]);

        $response = $this->getJson("/api/categories/" . $category->id);

        $response->assertStatus(200);
        expect($response->json("data.name"))->toBe("Gadget");
        expect($response->json("message"))->toBe("Category retrieved successfully");
        expect($response->json("statusCode"))->toBe(200);
    });

    it("Should return 404 if category is not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories/9999");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });

    it("Should return 404 if category is not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories/X-INVALID-ID");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });

});

describe("PATCH /api/categories/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);
        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => bcrypt("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be success update category by id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "Old Name"]);

        $response = $this->patchJson("/api/categories/" . $category->id, ["name" => "New Name"]);

        $response->assertStatus(200);
        expect($response->json("data.name"))->toBe("New Name");
        expect($response->json("message"))->toBe("Category updated successfully");
        expect($response->json("statusCode"))->toBe(200);

        $this->assertDatabaseHas("categories", ["id" => $category->id, "name" => "New Name"]);
    });

    it("Should return 404 if category not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/categories/9999", ["name" => "Updated"]);

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });

    it("Should fail if name is not unique", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Category::create(["name" => "Existing"]);
        $category = \App\Models\Category::create(["name" => "ToBeUpdated"]);

        $response = $this->patchJson("/api/categories/" . $category->id, ["name" => "Existing"]);

        $response->assertStatus(400);
        expect($response->json("errors.message.name.0"))->toBe("The name has already been taken.");
    });

    it("Should success even if payload is empty", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "KeepMe"]);

        $response = $this->patchJson("/api/categories/" . $category->id, []);

        $response->assertStatus(200);
        expect($response->json("message"))->toContain("success");
    });
});

describe("GET /api/categories", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => bcrypt("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        for ($i = 1; $i <= 30; $i++) {
            \App\Models\Category::create([
                "name" => "Category $i"
            ]);
        }
    });

    it("Should be success get first page of categories", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories");

        $response->assertStatus(200);

        expect($response->json("data"))->toHaveCount(10);
        expect($response->json("meta.current_page"))->toBe(1);
        expect($response->json("meta.total"))->toBe(30);
        expect($response->json("meta.last_page"))->toBe(3);

        expect($response->json("message"))->toBe("Categories retrieved successfully");
        expect($response->json("statusCode"))->toBe(200);
    });

    it("Should be success get second page with 15 per page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories?page=2&per_page=15");

        $response->assertStatus(200);

        expect($response->json("data"))->toHaveCount(15);
        expect($response->json("meta.current_page"))->toBe(2);
        expect($response->json("meta.per_page"))->toBe(15);
        expect($response->json("meta.total"))->toBe(30);
    });
});


describe("DELETE /api/categories/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category'
        ]);
    });


    it("Should be success delete category if not related to product", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/categories/" . $this->category->id);

        $response->assertStatus(200);
        expect($response->json("data.message"))->toBe("Category deleted successfully");
        expect($response->json("statusCode"))->toBe(200);

        $this->assertSoftDeleted('categories', [
            'id' => $this->category->id
        ]);
    });

    it("Should be failed to delete category if it has relation with product", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $unit = \App\Models\Unit::create([
            'name' => 'picis',
            'code' => 'pcs',
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Sample Category Kedua'
        ]);

        $supplier = \App\Models\Supplier::create([
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
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'description' => 'Sample description',
            'brand' => 'Brand X',
            'discount' => 0,
            'weight' => 1.5,
            'volume' => 0.75,
            'barcode' => '1234567890123',
            'images' => null,
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(400);
        expect($response->json("errors.message"))
            ->toBe("Cannot delete category because it is still associated with one or more products.");

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null,
        ]);
    });

    it("Should be failed to get category by id and return 404 not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories/99999");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });
    it("Should be failed to get category by id and return 404 not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/categories/X-INVALID-ID");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });


});


describe("PATCH /api/categories/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be success restoring a soft-deleted category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "Soft Deleted Category"]);
        $category->delete();

        $response = $this->patchJson("/api/categories/{$category->id}/restore");

        $response->assertStatus(200);
        expect($response->json("message"))->toBe("Category restored successfully");
        $this->assertDatabaseHas("categories", [
            "id" => $category->id,
            "deleted_at" => null
        ]);
    });

    it("Should fail restoring a category that is not soft-deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "Active Category"]);

        $response = $this->patchJson("/api/categories/{$category->id}/restore");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Cannot restore. Category has not been soft deleted.");
    });

    it("Should return 404 if category not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/categories/99999/restore");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });
});


describe("DELETE /api/categories/{id}/force", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be success force deleting a soft-deleted category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "To be force deleted"]);
        $category->delete();

        $response = $this->deleteJson("/api/categories/{$category->id}/force");

        $response->assertStatus(200);
        expect($response->json("data.message"))->toBe("Category deleted successfully");
        $this->assertDatabaseMissing("categories", [
            "id" => $category->id
        ]);
    });

    it("Should fail force deleting a category that is not soft-deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\Category::create(["name" => "Not soft deleted"]);

        $response = $this->deleteJson("/api/categories/{$category->id}/force");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Cannot force delete. Category has not been soft deleted.");
    });

    it("Should return 404 if category not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/categories/99999/force");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });

    it("Should return 404 if category not found even if id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/categories/X-INVALID-ID/force");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Category not found");
    });

    it("Should be failed to force delete category if it has relation with product", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $unit = \App\Models\Unit::create([
            'name' => 'picis',
            "code" => "pcs",
        ]);

        $category = \App\Models\Category::create([
            'name' => 'Sample Category Kedua'
        ]);

        $supplier = \App\Models\Supplier::create([
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
            'unit_id' => $unit->id,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'description' => 'Sample description',
            'brand' => 'Brand X',
            'discount' => 0,
            'weight' => 1.5,
            'volume' => 0.75,
            'barcode' => '1234567890123',
            'images' => null,
        ]);

        $category->delete();

        $response = $this->deleteJson("/api/categories/" . $category->id . "/force");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Cannot force delete category because it is still associated with one or more products.");

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    });

});
