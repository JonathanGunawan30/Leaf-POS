<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/expenses", function (){
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

    it("Should be successful create expense [finance]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->postJson("/api/expenses", [
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "description" => "Office Supplies",
            "note" => "Beli tinta printer"
        ]);


        $response->assertStatus(201);
        expect($response->json("message"))->toContain("success");
        expect($response->json())->toHaveKeys(["data", "message", "statusCode"]);

        expect($response->json("data.amount"))->toBe(1500000);
        expect($response->json("data.category_id"))->toBe($this->category->id);
        expect($response->json("data.user_id"))->toBe($this->finance->id);

        $this->assertDatabaseHas("expenses", [
            "amount" => 1500000,
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
        ]);
    });

    it("Should be successful create expense [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->postJson("/api/expenses", [
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "description" => "Office Supplies",
            "note" => "Beli tinta printer"
        ]);

        $response->assertStatus(201);
        expect($response->json("message"))->toContain("success");
        expect($response->json())->toHaveKeys(["data", "message", "statusCode"]);

        expect($response->json("data.amount"))->toBe(1500000);
        expect($response->json("data.category_id"))->toBe($this->category->id);
        expect($response->json("data.user_id"))->toBe($this->admin->id);

        $this->assertDatabaseHas("expenses", [
            "amount" => 1500000,
            "category_id" => $this->category->id,
            "user_id" => $this->admin->id,
        ]);
    });

    it("Should fail to create expense when unauthenticated", function () {
        $response = $this->postJson("/api/expenses", [
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
        ]);

        expect($response->status())->toBe(401);
        expect($response->json("errors.message"))->toBe("Unauthenticated.");
    });


    it("Should fail to create expense due to forbidden role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->postJson("/api/expenses", [
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
        ]);

        expect($response->status())->toBe(403);
        expect($response->json("errors.message"))->toBe("Forbidden");
    });

    it("Should fail to create expense when required fields are missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->postJson("/api/expenses", []);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message"))->toHaveKeys(["amount", "expense_date", "category_id"]);
    });
    it("Should fail to create expense with negative amount", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->postJson("/api/expenses", [
            "amount" => -50000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.amount"))->toBeArray();
    });

    it("Should fail to create expense with invalid category_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->postJson("/api/expenses", [
            "amount" => 100000,
            "expense_date" => now()->toDateString(),
            "category_id" => 9999,
        ]);

        expect($response->status())->toBe(400);
        expect($response->json("errors.message.category_id"))->toBeArray();
    });
    it("Should successfully create expense with only required fields", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $payload = [
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
        ];

        $response = $this->postJson("/api/expenses", $payload);

        expect($response->status())->toBe(201)
            ->and($response->json("message"))->toBe("Expense created successfully")
            ->and($response->json("data"))->toMatchArray([
                "amount" => 1500000,
                "expense_date" => now()->toDateString(),
                "category_id" => $this->category->id,
                "user_id" => $this->finance->id,
            ]);

        $this->assertDatabaseHas("expenses", [
            "amount" => 1500000,
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
        ]);
    });

});

describe("GET /expense/{id}", function (){
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
            "name" => "Expense Category"
        ]);

        $this->expense = \App\Models\Expense::query()->create([
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
            "description" => "Test expense",
            "note" => "Test note"
        ]);

    });
    it("Should be successful get expense detail by id [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses/{$this->expense->id}");

        $response->assertStatus(200);

        expect($response->json())->toHaveKey('data');
        expect($response->json('data.id'))->toEqual($this->expense->id);
        expect($response->json('data.description'))->toEqual($this->expense->description);
        expect($response->json('data.amount'))->toEqual((float) $this->expense->amount);

        expect($response->json('data.user'))->toMatchArray([
            'id' => $this->finance->id,
            'name' => $this->finance->name,
            'email' => $this->finance->email,
            'role' => [
                'id' => $this->finance->role->id,
                'name' => $this->finance->role->name
            ]
        ]);

        expect($response->json('data.category'))->toMatchArray([
            'id' => $this->category->id,
            'name' => $this->category->name
        ]);
    });


    it("Should be successful get expense detail by id [finance]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->getJson("/api/expenses/{$this->expense->id}");

        $response->assertStatus(200);
        expect($response->json('data.id'))->toEqual($this->expense->id);
        expect($response->json('data.user.name'))->toEqual($this->finance->name);
        expect($response->json('data.category.name'))->toEqual($this->category->name);
    });

    it("Should fail when expense not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $invalidId = 999999;

        $response = $this->getJson("/api/expenses/{$invalidId}");

        $response->assertStatus(404);

        expect($response->json())->toHaveKey('errors.message');
        expect($response->json('errors.message'))->toContain("not found");
    });


    it("Should fail when unauthenticated", function () {
        $response = $this->getJson("/api/expenses/{$this->expense->id}");

        $response->assertStatus(401);

        expect($response->json())->toHaveKey('errors.message');
        expect($response->json('errors.message'))->toContain("Unauthenticated");
    });


    it("Should fail when accessing other user's expense", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->getJson("/api/expenses/{$this->expense->id}");

        $response->assertStatus(403);

        expect($response->json())->toHaveKey('errors.message');
        expect($response->json('errors.message'))->toContain("Forbidden");

    });

    it("Should fail when expense not found even tho id is string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $invalidId = "X-WRONG-ID";

        $response = $this->getJson("/api/expenses/{$invalidId}");

        $response->assertStatus(404);

        expect($response->json())->toHaveKey('errors.message');
        expect($response->json('errors.message'))->toContain("not found");
    });

});

describe("GET /expenses", function (){
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
            "name" => "Expense Category"
        ]);

        for ($i = 1; $i <= 50; $i++) {
            \App\Models\Expense::query()->create([
                "amount" => 1500000 + $i * 1000,
                "expense_date" => now()->subDays($i)->toDateString(),
                "category_id" => $this->category->id,
                "user_id" => $i % 2 === 0 ? $this->admin->id : $this->finance->id,
                "description" => "Test expense $i",
                "note" => "Test note $i"
            ]);
        }


    });
    it("Should be successful get expense list [finance]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->getJson("/api/expenses");

        $response->assertStatus(200);

        expect($response->json('data'))->toBeArray();
        expect($response->json('data.0'))->toHaveKeys([
            'id',
            'description',
            'note',
            'expense_date',
            'amount',
            'user',
            'category'
        ]);
        expect($response->json('data.0.user'))->toHaveKeys([
            'id',
            'name',
            'email',
            'role'
        ]);
        expect($response->json('data.0.category'))->toHaveKeys([
            'id',
            'name'
        ]);
    });

    it("Should be successful get expense list [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses");

        $response->assertStatus(200);
        expect($response->json('data'))->toBeArray();
    });
    it("Should fail get expense list when unauthenticated", function () {
        $response = $this->getJson("/api/expenses");

        $response->assertStatus(401);
    });
    it("Should return empty list if filter by unknown user_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses?user_id=999999");

        $response->assertStatus(200);
        expect($response->json('data'))->toBeArray()->toHaveCount(0);
    });
    it("Should return filtered list with search", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses?search=Test expense");

        $response->assertStatus(200);

        $data = $response->json('data');

        foreach ($data as $item) {
            expect($item['description'])->toContain('Test expense');
        }
    });
    it("Should filter expense by end_date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $endDate = now()->toDateString();

        $response = $this->getJson("/api/expenses?end_date={$endDate}");

        $response->assertStatus(200);

        foreach ($response->json('data') as $expense) {
            expect($expense['expense_date'])->toBeLessThanOrEqual($endDate);
        }
    });
    it("Should filter expense by start_date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $startDate = now()->subDays(10)->toDateString();

        $response = $this->getJson("/api/expenses?start_date={$startDate}");

        $response->assertStatus(200);

        foreach ($response->json('data') as $expense) {
            expect($expense['expense_date'])->toBeGreaterThanOrEqual($startDate);
        }
    });
    it("Should filter expense by category_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses?category_id={$this->category->id}");

        $response->assertStatus(200);

        foreach ($response->json('data') as $expense) {
            expect($expense['category']['id'])->toEqual($this->category->id);
        }
    });
    it("Should filter expense by user_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses?user_id={$this->finance->id}");

        $response->assertStatus(200);

        foreach ($response->json('data') as $expense) {
            expect($expense['user']['id'])->toEqual($this->finance->id);
        }
    });
    it("Should filter expense by start_date and category_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $startDate = now()->subDays(7)->toDateString();

        $response = $this->getJson("/api/expenses?start_date={$startDate}&category_id={$this->category->id}");

        $response->assertStatus(200);

        foreach ($response->json('data') as $expense) {
            expect($expense['expense_date'])->toBeGreaterThanOrEqual($startDate);
            expect($expense['category']['id'])->toEqual($this->category->id);
        }
    });
    it("Should paginate expenses properly", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/expenses?page=2&per_page=10");

        $response->assertStatus(200);

        $json = $response->json();

        expect($json)->toHaveKeys(['meta', 'data']);

        expect($json['meta'])->toMatchArray([
            'current_page' => 2,
            'per_page' => 10,
        ]);

        expect($json['meta']['from'])->toBeInt();
        expect($json['meta']['to'])->toBeInt();
        expect($json['meta']['total'])->toBeInt();
        expect($json['meta']['last_page'])->toBeInt();

        expect(count($json['data']))->toBeLessThanOrEqual(10);
    });


    it("Should filter expenses with complete combination of filters", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $startDate = now()->subDays(30)->toDateString();
        $endDate = now()->toDateString();
        $categoryId = $this->category->id;
        $userId = $this->finance->id;
        $search = 'Test expense';

        $response = $this->getJson("/api/expenses?start_date={$startDate}&end_date={$endDate}&category_id={$categoryId}&user_id={$userId}&search={$search}&page=1&per_page=5");

        $response->assertStatus(200);

        $json = $response->json();

        expect($json['per_page'] ?? count($json['data']))->toBeLessThanOrEqual(5);

        foreach ($json['data'] as $expense) {
            expect($expense['expense_date'])->toBeGreaterThanOrEqual($startDate);
            expect($expense['expense_date'])->toBeLessThanOrEqual($endDate);
            expect($expense['category']['id'])->toEqual($categoryId);
            expect($expense['user']['id'])->toEqual($userId);
            expect(strtolower($expense['description'] . $expense['note']))->toContain(strtolower($search));
        }
    });



});

describe("PATCH /api/expenses/{id}", function (){
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
            "name" => "Expense Category"
        ]);

        $this->expense = \App\Models\Expense::query()->create([
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
            "description" => "Test expense",
            "note" => "Test note"
        ]);

    });

    it("Should be successfully updated all fields expense [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "amount" => 2000000,
            "expense_date" => now()->addDay()->toDateString(),
            "category_id" => $this->category->id,
            "description" => "UPDATED DESC",
            "note" => "UPDATED Note"
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data"]);
        expect($response->json())->toHaveKeys(["message"]);
        expect($response->json())->toHaveKeys(["statusCode"]);
        expect($response->json("data.description"))->toBe("UPDATED DESC");
    });

    it("Should be successfully updated one field expense [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "description" => "UPDATED DESC 2",
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data"]);
        expect($response->json())->toHaveKeys(["message"]);
        expect($response->json())->toHaveKeys(["statusCode"]);
        expect($response->json("data.description"))->toBe("UPDATED DESC 2");
    });

    it("Should be successfully updated all fields expense [finance]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $payLoad = [
            "amount" => 2000000,
            "expense_date" => now()->addDay()->toDateString(),
            "category_id" => $this->category->id,
            "description" => "UPDATED DESC",
            "note" => "UPDATED Note"
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data"]);
        expect($response->json())->toHaveKeys(["message"]);
        expect($response->json())->toHaveKeys(["statusCode"]);
        expect($response->json("data.description"))->toBe("UPDATED DESC");
    });

    it("Should fail to update expense when unauthenticated", function () {
        $payLoad = [
            "description" => "Unauthenticated update attempt"
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(401);
        expect($response->json())->toHaveKey("errors");
    });
    it("Should fail to update expense when unauthorized role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $payLoad = [
            "description" => "Inventory trying to update expense"
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(403);
        expect($response->json())->toHaveKey("errors");
    });

    it("Should fail to update expense with invalid amount", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "amount" => -5000
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(400);
        expect($response->json())->toHaveKey("errors");
        expect($response->json("errors.message.amount"))->not->toBeEmpty();
    });

    it("Should fail to update expense with invalid category_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "category_id" => 99999
        ];

        $response = $this->patchJson("/api/expenses/" . $this->expense->id, $payLoad);

        $response->assertStatus(400);
        expect($response->json())->toHaveKey("errors");
        expect($response->json("errors.message.category_id"))->not->toBeEmpty();
    });

    it("Should fail to update expense with invalid expense id and return not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "description" => "UPDATED"
        ];

        $response = $this->patchJson("/api/expenses/" . 999999, $payLoad);

        $response->assertStatus(404);
        expect($response->json())->toHaveKey("errors");
        expect($response->json("errors.message"))->not->toBeEmpty();
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should fail to update expense with invalid expense id even tho id is string and return not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "description" => "UPDATED"
        ];

        $response = $this->patchJson("/api/expenses/" . "X-INVALID-ID", $payLoad);

        $response->assertStatus(404);
        expect($response->json())->toHaveKey("errors");
        expect($response->json("errors.message"))->not->toBeEmpty();
        expect($response->json("errors.message"))->toContain("not found");
    });



});
describe("DELETE /api/expenses/{id}", function (){
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
            "name" => "Expense Category"
        ]);

        $this->expense = \App\Models\Expense::query()->create([
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
            "description" => "Test expense",
            "note" => "Test note"
        ]);

    });

    it("Should be success deleted expense [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expenses/" . $this->expense->id);

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");

        $isDeleted = \App\Models\Expense::withTrashed()->find($this->expense->id);
        expect($isDeleted)->not->toBeNull();
        expect($isDeleted->trashed())->toBeTrue();

        $user = \App\Models\User::find($this->expense->user_id);
        expect($user)->not()->toBeNull();

        $category = \App\Models\ExpenseCategory::find($this->expense->category_id);
        expect($category)->not()->toBeNull();
    });


    it("Should be success deleted expense [finance]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->deleteJson("/api/expenses/" . $this->expense->id);

        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");

        $isDeleted = \App\Models\Expense::withTrashed()->find($this->expense->id);
        expect($isDeleted)->not->toBeNull();
        expect($isDeleted->trashed())->toBeTrue();

    });

    it("Should be failed deleted expense and return forbidden 403 [inventory]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->deleteJson("/api/expenses/" . $this->expense->id);

        $response->assertStatus(403);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("Forbidden");

        $isDeleted = \App\Models\Expense::find($this->expense->id);
        expect($isDeleted)->not()->toBeNull();

    });

    it("Should be failed deleted expense and return notfound 404", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expenses/" . 834765);

        $response->assertStatus(404);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");


    });

    it("Should be failed deleted expense and return notfound 404 even tho id is string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expenses/X-INVALID-ID" );

        $response->assertStatus(404);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");


    });

});

describe("PATCH /api/expenses/{id}/restore", function (){
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
            "name" => "Expense Category"
        ]);

        $this->expense = \App\Models\Expense::query()->create([
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
            "description" => "Test expense",
            "note" => "Test note"
        ]);

    });

    it("Should be successful restore deleted expense [admin]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $expense  = \App\Models\Expense::find($this->expense->id);
        $expense->delete();

        $response = $this->patchJson("/api/expenses/"  . $this->expense->id . "/restore");

        $response->assertStatus(200);

        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");
    });

    it("Should be successful restore deleted expense [finance]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $expense  = \App\Models\Expense::find($this->expense->id);
        $expense->delete();

        $response = $this->patchJson("/api/expenses/"  . $this->expense->id . "/restore");
        $response->assertStatus(200);

        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");
    });

    it("Should be failed restore deleted expense and return 403 forbidden[inventory]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $expense  = \App\Models\Expense::find($this->expense->id);
        $expense->delete();

        $response = $this->patchJson("/api/expenses/"  . $this->expense->id . "/restore");

        $response->assertStatus(403);

        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("Forbidden");
    });

    it("Should be failed restore deleted expense and return 404 notfound", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expenses/"  . 3294875 . "/restore");

        $response->assertStatus(404);

        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should be failed restore deleted expense and return 404 notfound even tho id is invalid (string)", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expenses/X-INVALID-ID/restore");

        $response->assertStatus(404);

        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should be failed restore deleted expense and return 400 not deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/expenses/". $this->expense->id ."/restore");

        $response->assertStatus(400);

        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not deleted");
    });

    it("Should fail on second restore attempt with 400", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $expense = \App\Models\Expense::find($this->expense->id);
        $expense->delete();

        $this->patchJson("/api/expenses/{$this->expense->id}/restore")->assertStatus(200);

        $secondAttempt = $this->patchJson("/api/expenses/{$this->expense->id}/restore");
        $secondAttempt->assertStatus(400);
        expect($secondAttempt->json("errors.message"))->toContain("not deleted");
    });
    it("Should fail to restore expense that was force deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $expense = \App\Models\Expense::find($this->expense->id);
        $expense->delete();
        $expense->forceDelete();

        $response = $this->patchJson("/api/expenses/{$this->expense->id}/restore");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toContain("not found");
    });
    it("Should fail to restore expense when not authenticated", function () {
        $this->expense->delete();

        $response = $this->patchJson("/api/expenses/{$this->expense->id}/restore");

        $response->assertStatus(401);
        expect($response->json("errors.message"))->toContain("Unauthenticated");
    });


});

describe("DELETE /api/expense/{id}/force", function (){
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
            "name" => "Expense Category"
        ]);

        $this->expense = \App\Models\Expense::query()->create([
            "amount" => 1500000,
            "expense_date" => now()->toDateString(),
            "category_id" => $this->category->id,
            "user_id" => $this->finance->id,
            "description" => "Test expense",
            "note" => "Test note"
        ]);

    });

    it("Should be success force delete expense [admin]", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);
       $this->expense->delete();
       $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");

       $response->assertStatus(200);

       expect($response->json())->toHaveKeys(["data.message"]);
       expect($response->json("data.message"))->toContain("success");

       $isDeleted = \App\Models\Expense::withTrashed()->find($this->expense->id);
       expect($isDeleted)->toBeNull();
    });

    it("Should be success force delete expense [finance]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);
        $this->expense->delete();
        $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");

        $response->assertStatus(200);

        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");
    });

    it("Should be failed force delete expense and return forbidden [inventory]", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);
        $this->expense->delete();
        $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");

        $response->assertStatus(403);

        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("Forbidden");
    });

    it("Should be failed force delete expense and return 404 not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expenses/4530983490580/force");

        $response->assertStatus(404);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should be failed force delete expense and return 404 not found even tho id is invalid (string)" , function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/expenses/X-INVALID-ID/force");

        $response->assertStatus(404);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");
    });

    it("Should be failed force delete expense and return 400 not deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");
        $response->assertStatus(400);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not deleted");
    });

    it("Should be success force delete expense even tho delete it twice", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->expense->delete();
        $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");
        $response->assertStatus(200);
        expect($response->json())->toHaveKeys(["data.message"]);
        expect($response->json("data.message"))->toContain("success");
        $response = $this->deleteJson("/api/expenses/" . $this->expense->id . "/force");
        $response->assertStatus(404);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("not found");
    });


});

describe("GET /api/expenses/report", function (){
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
            "name" => "Expense Category"
        ]);

        for ($i = 1; $i <= 200; $i++) {
            \App\Models\Expense::query()->create([
                'amount' => rand(10000, 5000000),
                'expense_date' => now()->subDays(rand(0, 60))->toDateString(),
                'category_id' => $this->category->id,
                'user_id' => $i % 2 === 0 ? $this->admin->id : $this->finance->id,
                'description' => "Test expense {$i}",
                'note' => "Note {$i}"
            ]);
        }

    });
    it("Should be success export expense report as Excel [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/expense-reports?format=excel");

        $response->assertStatus(200);
        $response->assertHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        $response->assertHeader("Content-Disposition");

    });

    it("Should export filtered expense report as Excel [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $queryParams = http_build_query([
            'format' => 'excel',
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-20',
            'category_id' => $this->category->id,
            'user_id' => $this->finance->id,
        ]);

        $response = $this->get("/api/expense-reports?" . $queryParams);

        $response->assertStatus(200);
        $response->assertHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        $response->assertHeader("Content-Disposition");
    });

    it("Should not allow export without authentication", function () {
        $response = $this->getJson("/api/expense-reports?format=excel");

        $response->assertStatus(401);
        expect($response->json())->toHaveKeys(["errors.message"]);
        expect($response->json("errors.message"))->toContain("Unauthenticated");
    });
    it("Should fail export with invalid date format", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $queryParams = http_build_query([
            'format' => 'excel',
            'start_date' => 'invalid-date',
            'end_date' => '2025-03-01',
        ]);

        $response = $this->get("/api/expense-reports?" . $queryParams);

        $response->assertStatus(400);
        $response->assertJsonStructure([
            'errors' => [
                'message' => [
                    'start_date',
                ],
            ],
        ]);
    });

    it("Should not allow non-admin or finance to export report", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->get("/api/expense-reports?format=excel");

        $response->assertStatus(403);
        expect($response->json("errors.message"))->toBe("Forbidden");
    });

    it("Should export empty report if no data match filter", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $queryParams = http_build_query([
            'format' => 'excel',
            'start_date' => '1990-01-01',
            'end_date' => '1990-12-31',
        ]);

        $response = $this->get("/api/expense-reports?" . $queryParams);

        $response->assertStatus(200);
        $response->assertHeader("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    });

    it("Should return error if end_date is before start_date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $queryParams = http_build_query([
            'format' => 'excel',
            'start_date' => '2025-03-01',
            'end_date' => '2025-01-01',
        ]);

        $response = $this->get("/api/expense-reports?" . $queryParams);

        $response->assertStatus(400);
        expect($response['errors']['message'])->toHaveKey('end_date');
    });

    it("Should export full expense report without filters as Excel [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/expense-reports?format=excel");
        $response->assertStatus(200);
    });

    it("Should export filtered expense report as PDF [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $queryParams = http_build_query([
            'format' => 'pdf',
            'start_date' => '2025-03-01',
            'end_date' => '2025-03-21',
            'category_id' => $this->category->id,
            'user_id' => $this->finance->id,
        ]);

        $response = $this->get("/api/expense-reports?" . $queryParams);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

    });

    it("Should export all expenses without filters as PDF [admin]", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/expense-reports?format=pdf");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertNotEmpty($response->getContent());
    });
    it("Should fail to export if invalid format is given", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/expense-reports?format=invalid_format");

        $response->assertStatus(400);

        expect($response->json("errors.message"))->toContain("Invalid export format");
    });

    it("Should fail if start_date is after end_date", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/expense-reports?" . http_build_query([
                'format' => 'pdf',
                'start_date' => '2025-04-01',
                'end_date' => '2025-03-01',
            ]));

        $response->assertStatus(400);
        expect($response->json("errors.message"))->not->toBeNull();
    });



});
