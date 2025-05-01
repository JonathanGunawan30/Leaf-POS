<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/expense-categories", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });
   it("Should be successful when Admin create new expense categories", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => "Transport"
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(201);
        expect($response->json())->toHaveKey('data.name');
        expect($response->json("data.name"))->toBe("Transport");
        \Pest\Laravel\assertDatabaseHas("expense_categories", [
            "name" => "Transport"
        ]);
   });

    it("Should be successful when Finance create new expense categories", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $payload = [
            "name" => "Transport"
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(201);
        expect($response->json())->toHaveKey('data.name');
        expect($response->json("data.name"))->toBe("Transport");
        \Pest\Laravel\assertDatabaseHas("expense_categories", [
            "name" => "Transport"
        ]);
    });

    it("Should fail when Inventory tries to create new expense category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $payload = [
            "name" => "Transport"
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(403);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail with validation error when name is missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message.name");
    });

    it("Should fail with validation error when name is empty string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => ""
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail when name is longer than 100 characters", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => str_repeat("a", 101)
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail when name is not a string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => 12345
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail when user is unauthorized (not authenticated)", function () {
        $payload = [
            "name" => "Transport"
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(401);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail when category name already exists", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\ExpenseCategory::query()->create([
            "name" => "Transport"
        ]);

        $payload = [
            "name" => "Transport"
        ];

        $response = $this->postJson("/api/expense-categories", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

});

describe("PATCH /api/expense-categories/{id}", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successful when Admin update new expense categories", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $expenseCategory = \App\Models\ExpenseCategory::query()->create([
            "name" => "Transport"
        ]);

        $payload = [
            "name" => "Updated Transport"
        ];

        $response = $this->patchJson("/api/expense-categories/{$expenseCategory->id}", $payload);

        expect($response)->status()->toBe(200);
        expect($response->json())->toHaveKey('data.name');
        expect($response->json("data.name"))->toBe("Updated Transport");

        \Pest\Laravel\assertDatabaseHas("expense_categories", [
            "id" => $expenseCategory->id,
            "name" => "Updated Transport"
        ]);
    });

    it("Should be successful when Finance update new expense categories", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $expenseCategory = \App\Models\ExpenseCategory::query()->create([
            "name" => "Transport"
        ]);

        $payload = [
            "name" => "Updated Transport"
        ];

        $response = $this->patchJson("/api/expense-categories/{$expenseCategory->id}", $payload);


        expect($response)->status()->toBe(200);
        expect($response->json())->toHaveKey('data.name');
        expect($response->json("data.name"))->toBe("Updated Transport");

        \Pest\Laravel\assertDatabaseHas("expense_categories", [
            "id" => $expenseCategory->id,
            "name" => "Updated Transport"
        ]);
    });

    it("Should be forbidden when Inventory tries to update expense category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $category = \App\Models\ExpenseCategory::create(["name" => "Fuel"]);

        $payload = ["name" => "Updated Fuel"];

        $response = $this->patchJson("/api/expense-categories/{$category->id}", $payload);

        expect($response)->status()->toBe(403);
        expect($response->json("errors.message"))->toBe("Forbidden");
    });

    it("Should be unauthorized when unauthenticated user tries to update", function () {
        $category = \App\Models\ExpenseCategory::create(["name" => "Travel"]);

        $payload = ["name" => "Updated Travel"];

        $response = $this->patchJson("/api/expense-categories/{$category->id}", $payload);

        expect($response)->status()->toBe(401);
        expect($response->json("errors.message"))->toBe("Unauthenticated.");
    });

    it("Should fail validation when name is empty", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\ExpenseCategory::create(["name" => "Internet"]);

        $payload = ["name" => ""];

        $response = $this->patchJson("/api/expense-categories/{$category->id}", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should fail validation when name already exists", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\ExpenseCategory::create(["name" => "Transport"]);

        $category = \App\Models\ExpenseCategory::create(["name" => "Fuel"]);

        $payload = ["name" => "Transport"];

        $response = $this->patchJson("/api/expense-categories/{$category->id}", $payload);

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
    });

    it("Should return 404 when expense category not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = ["name" => "Nonexistent"];

        $response = $this->patchJson("/api/expense-categories/999", $payload);

        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Expense category not found");
    });

    it("Should be successful even when updating with the same name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\ExpenseCategory::create([
            "name" => "Transport"
        ]);

        $payload = ["name" => "Transport"];

        $response = $this->patchJson("/api/expense-categories/{$category->id}", $payload);

        expect($response)->status()->toBe(200);
        expect($response->json("data.name"))->toBe("Transport");
    });



});

describe("GET /api/expense-categories", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        for ($i = 0; $i < 50; $i ++){
            \App\Models\ExpenseCategory::query()->create([
               "name" => "Category - " . $i
            ]);
        }
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });
   it("Should be successfully get list of Expense Categories", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->finance);

       $response = $this->get('/api/expense-categories');

       expect($response)->status()->toBe(200);
       expect($response->json())->toHaveKey("data");
       expect($response->json("data"))->toBeArray();
       expect($response->json("data.0.name"))->toContain("Category");
   });

    it("Should fail when user is unauthorized", function () {
        $response = $this->getJson('/api/expense-categories');


        expect($response)->status()->toBe(401);
        expect($response->json())->toHaveKey('errors.message');
    });

    it("Should be failed(forbidden) when Inventory get list of Expense Categories", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->getJson('/api/expense-categories');

        expect($response)->status()->toBe(403);
        expect($response->json())->toHaveKey('errors.message');
    });

    it("Should return paginated result with meta information", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/expense-categories?per_page=5');

        expect($response->json())->toHaveKeys(['data', 'meta', 'links']);
        expect($response->json('meta'))->toHaveKeys(['current_page', 'per_page', 'total']);
    });

    it("Should respect per_page query param", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->getJson('/api/expense-categories?per_page=3');

        expect(count($response->json('data')))->toBeLessThanOrEqual(3);
    });

    it("Should return empty data when no categories exist", function () {
        \App\Models\ExpenseCategory::query()->delete();

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/expense-categories');

        expect($response->status())->toBe(200);
        expect($response->json('data'))->toBeArray()->toBeEmpty();
    });
    it("Should return correct page based on page query", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/expense-categories?page=2&per_page=10');

        expect($response->status())->toBe(200);
        expect($response->json('meta.current_page'))->toBe(2);
        expect(count($response->json('data')))->toBeLessThanOrEqual(10);
    });

    it("Should default to page 1 if no page param provided", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->getJson('/api/expense-categories');

        expect($response->json('meta.current_page'))->toBe(1);
    });
    it("Should fail if per_page is not a number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/expense-categories?per_page=abc');

        expect($response->status())->toBe(422);
        expect($response->json())->toHaveKey('errors.message');
    });

});

describe("DELETE /api/expense-categories/{id}", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->category = \App\Models\ExpenseCategory::query()->create([
            "name" => "Expense Category Data"
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });
   it("Should be successful deleted expense category [finance]", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->finance);

       $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);

       expect($response)->status()->toBe(200);
       expect(\App\Models\ExpenseCategory::withTrashed()->find($this->category->id)->deleted_at)->not->toBeNull();
       expect($response->json())->toHaveKey("data.message");
       expect($response->json("data.message"))->toContain("deleted success");
   });

    it("Should be successful deleted expense category [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);

        expect($response)->status()->toBe(200);
        expect($response->json())->toHaveKey("data.message");
        expect($response->json("data.message"))->toContain("deleted success");
    });

    it("Should be failed deleted expense category and return forbidden [inventory]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);

        expect($response)->status()->toBe(403);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Forbidden");
    });

    it("Should be failed deleted expense category and return unauthorized ", function (){

        $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);

        expect($response)->status()->toBe(401);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Unauthenticated.");
    });

    it("Should not delete expense category if it is used", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\ExpenseCategory::create(["name" => "Travel"]);
        \App\Models\Expense::create([
            "category_id" => $category->id,
            "amount" => 10000,
            "user_id" => $this->admin->id,
            "description" => "Test expense category Travel",
            "expense_date" => now()->toDateString()
        ]);


        $response = $this->deleteJson("/api/expense-categories/{$category->id}");

        expect($response)->status()->toBe(400);
        expect($response->json("errors.message"))->toBe("Cannot delete this category because it is associated with existing expenses.");
    });
    it("Should return 404 when trying to delete non-existing expense category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/expense-categories/9999");

        $response->assertStatus(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should return 404 when trying to delete already deleted category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->deleteJson("/api/expense-categories/" . $this->category->id);
        $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);
        expect($response)->status()->toBe(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should return 400 when trying to delete non-existing expense category with string id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/expense-categories/X-WRONG-ID");

        $response->assertStatus(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Expense category not found");
    });

    it("Soft deleted category should still exist in DB (withTrashed)", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->deleteJson("/api/expense-categories/" . $this->category->id);

        $categoryInDb = \App\Models\ExpenseCategory::withTrashed()->find($this->category->id);
        expect($categoryInDb)->not->toBeNull();
        expect($categoryInDb->deleted_at)->not->toBeNull();
    });

    it("Should paginate only active expense categories", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        for ($i = 1; $i <= 15; $i++) {
            \App\Models\ExpenseCategory::create([
                "name" => "Category $i"
            ]);
        }

        \App\Models\ExpenseCategory::first()->delete();

        $response = $this->getJson("/api/expense-categories?per_page=10");

        $response->assertStatus(200);
        expect(count($response->json("data")))->toBe(10);

        $deletedIds = \App\Models\ExpenseCategory::onlyTrashed()->pluck("id");
        $responseIds = collect($response->json("data"))->pluck("id");

        foreach ($responseIds as $id) {
            expect($deletedIds)->not->toContain($id);
        }
    });

    it("Inventory should not be able to delete already soft deleted category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->deleteJson("/api/expense-categories/" . $this->category->id);

        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);
        $response = $this->deleteJson("/api/expense-categories/" . $this->category->id);

        $response->assertStatus(403);
    });

});

describe("PATCH /api/expense-categories/{id}/restore", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->category = \App\Models\ExpenseCategory::query()->create([
            "name" => "Expense Category Data"
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successful restore expense category [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/" . $this->category->id);

        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore");

        expect($response)->assertStatus(200);
        expect($response->json())->toHaveKey("data.message");
        expect($response->json("data.message"))->toContain("Expense category restored successfully");

        $restored = \App\Models\ExpenseCategory::find($this->category->id);
        expect($restored)->not->toBeNull();
        expect($restored->deleted_at)->toBeNull();
    });


    it("Should be successful restore expense category [finance]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/" . $this->category->id);

        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore");

        expect($response)->assertStatus(200);
        expect($response->json())->toHaveKey("data.message");
        expect($response->json("data.message"))->toContain("Expense category restored successfully");

        $restored = \App\Models\ExpenseCategory::find($this->category->id);
        expect($restored)->not->toBeNull();
        expect($restored->deleted_at)->toBeNull();
    });

    it("Should be forbidden when trying to restore expense category [inventory]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $this->delete("/api/expense-categories/" . $this->category->id);

        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore");

        expect($response)->assertStatus(403);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Forbidden");
    });

    it("Should be unauthorized when not authenticated", function () {
        $this->delete("/api/expense-categories/" . $this->category->id);

        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore");

        expect($response)->assertStatus(401);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Unauthenticated.");
    });
    it("Should fail to restore active expense category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore");

        expect($response)->assertStatus(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should return 404 for invalid or non-deleted category id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expense-categories/9999999/restore");

        expect($response)->assertStatus(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should return 404 for string id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expense-categories/X-WRONG-ID/restore");

        expect($response)->assertStatus(404);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should return 404 if category is already restored", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/" . $this->category->id);

        $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore"); // Restore pertama
        $response = $this->patchJson("/api/expense-categories/" . $this->category->id . "/restore"); // Restore kedua

        expect($response)->assertStatus(404);
        expect($response->json("errors.message"))->toContain("not found");
    });

});

describe("DELETE /api/expense-categories/{id}/force", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->financeRole = \App\Models\Role::query()->create([
            "name" => "Finance"
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

        $this->finance = \App\Models\User::query()->create([
            "name" => "finance",
            "email" => "finance@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->financeRole->id
        ]);

        $this->inventory = \App\Models\User::query()->create([
            "name" => "inventory",
            "email" => "inventory@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->inventoryRole->id
        ]);

        $this->category = \App\Models\ExpenseCategory::query()->create([
            "name" => "Expense Category Data"
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successful force delete expense category [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/{$this->category->id}");
        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(200);
        expect($response->json("data.message"))->toContain("permanently deleted");
        expect(\App\Models\ExpenseCategory::withTrashed()->find($this->category->id))->toBeNull();
    });

    it("Should be successful force delete expense category [finance]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $this->delete("/api/expense-categories/{$this->category->id}");
        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(200);
        expect($response->json("data.message"))->toContain("permanently deleted");
        expect(\App\Models\ExpenseCategory::withTrashed()->find($this->category->id))->toBeNull();
    });

    it("Should fail to force delete if category is not soft deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(400);
        expect($response->json("errors.message"))->toContain("must be soft deleted");
    });

    it("Should return 404 on force delete for non-existing category", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/expense-categories/9999/force");

        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should fail to force delete expense category due to unauthenticated", function () {

        $response = $this->deleteJson("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(401);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Unauthenticated.");
    });
    it("Should fail to force delete expense category due to forbidden role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $this->delete("/api/expense-categories/{$this->category->id}");
        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(403);
        expect($response->json("errors.message"))->toContain("Forbidden");
    });
    it("Should fail to force delete because the category is already permanently deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/{$this->category->id}");
        $this->delete("/api/expense-categories/{$this->category->id}/force");

        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");
    });
    it("Should fail to force delete with invalid string id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/expense-categories/X-WRONG-ID/force");

        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should ignore repeated soft deletes and still allow force delete", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->delete("/api/expense-categories/{$this->category->id}");
        $this->delete("/api/expense-categories/{$this->category->id}");
        $response = $this->delete("/api/expense-categories/{$this->category->id}/force");

        expect($response)->status()->toBe(200);
        expect(\App\Models\ExpenseCategory::withTrashed()->find($this->category->id))->toBeNull();
    });
    it("Should fail to force delete expense category that still exists because failed soft delete still has relation", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\ExpenseCategory::create(['name' => 'Ops']);
        \App\Models\Expense::create([
            'category_id' => $category->id,
            'amount' => 50000,
            'user_id' => $this->admin->id,
            'description' => 'Biaya Operasional',
            'expense_date' => now()->toDateString()
        ]);

        $this->delete("/api/expense-categories/{$category->id}");

        $response = $this->delete("/api/expense-categories/{$category->id}/force");

        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKey("errors.message");
        expect($response->json("errors.message"))->toContain("Expense category must be soft deleted before force delete");
    });
    it("Should fail to force delete if category soft deleted manually and has expenses", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $category = \App\Models\ExpenseCategory::create(['name' => 'Manual Delete Test']);
        \App\Models\Expense::create([
            'category_id' => $category->id,
            'amount' => 150000,
            'user_id' => $this->admin->id,
            'description' => 'Test force delete langsung',
            'expense_date' => now()->toDateString(),
        ]);

        $category->delete();

        $response = $this->delete("/api/expense-categories/{$category->id}/force");

        expect($response)->status()->toBe(400);
        expect($response->json("errors.message"))->toContain("related expenses");
    });

});



