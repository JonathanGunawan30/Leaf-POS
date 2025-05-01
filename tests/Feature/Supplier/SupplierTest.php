<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/suppliers", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be successful create new supplier", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "John Doe",
            "company_name" => "PT Sumber Makmur",
            "email" => "john.doe@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Mandiri",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP123456",
            "nib_number" => "NIB987654",
            "business_type" => "Distributor",
            "note" => "Supplier utama untuk bahan baku"
        ];

        $response = $this->postJson("/api/suppliers", $payLoad);

        $response->assertStatus(201);

        expect($response->json("message"))->toBe("Supplier created successfully");

        foreach ($payLoad as $key => $value) {
            expect($response->json("data.$key"))->toBe($value);
        }

        $this->assertDatabaseHas("suppliers", [
            "email" => "john.doe@example.com",
            "company_name" => "PT Sumber Makmur",
            "name" => "John Doe",
            "business_type" => "Distributor",
        ]);
    });

    it("Should fail when required fields are missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [];

        $response = $this->postJson("/api/suppliers", $payload);

        $response->assertStatus(400);
        $errors = $response->json("errors.message");
        expect($response->json("errors.message.name.0"))->toBe("The name field is required.");
        expect($errors)->toHaveKeys([
            "name",
            "company_name",
            "email",
            "phone",
            "city",
            "postal_code",
            "province",
            "country",
            "address",
            "bank_account",
            "bank_name",
            "business_type"
        ]);
    });

    it("Should fail when email is not unique", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Supplier::create([
            "name" => "Existing",
            "company_name" => "PT Lama",
            "email" => "duplikat@example.com",
            "phone" => "08123456789",
            "city" => "Bandung",
            "postal_code" => "40111",
            "province" => "Jawa Barat",
            "country" => "Indonesia",
            "address" => "Jl. Lama No.1",
            "bank_account" => "12345678",
            "bank_name" => "BRI",
            "business_type" => "Manufaktur",
        ]);

        $payload = [
            "name" => "Baru",
            "company_name" => "PT Baru",
            "email" => "duplikat@example.com",
            "phone" => "0899988888",
            "city" => "Surabaya",
            "postal_code" => "60234",
            "province" => "Jawa Timur",
            "country" => "Indonesia",
            "address" => "Jl. Baru No.2",
            "bank_account" => "87654321",
            "bank_name" => "Mandiri",
            "business_type" => "Distribusi"
        ];

        $response = $this->postJson("/api/suppliers", $payload);

        $response->assertStatus(400);
        expect($response->json("errors.message.email.0"))->toBe("The email has already been taken.");
    });

    it("Should fail to create supplier if not authenticated", function () {
        $payload = [
            "name" => "Unauthorized User",
            "company_name" => "PT Tidak Boleh",
            "email" => "unauth@example.com",
            "phone" => "0811111111",
            "city" => "Solo",
            "postal_code" => "57111",
            "province" => "Jawa Tengah",
            "country" => "Indonesia",
            "address" => "Jl. Lain No.3",
            "bank_account" => "00001111",
            "bank_name" => "BNI",
            "business_type" => "Retail"
        ];

        $response = $this->postJson("/api/suppliers", $payload);

        $response->assertStatus(401);
    });

    it("Should create supplier even if optional fields are missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => "No Optional",
            "company_name" => "PT Minimalis",
            "email" => "minimal@example.com",
            "phone" => "0811222333",
            "city" => "Malang",
            "postal_code" => "65123",
            "province" => "Jawa Timur",
            "country" => "Indonesia",
            "address" => "Jl. Pendek No.4",
            "bank_account" => "987654321",
            "bank_name" => "Bank Jatim",
            "business_type" => "Supplier"
            // tanpa npwp, siup, nib, note
        ];

        $response = $this->postJson("/api/suppliers", $payload);

        $response->assertStatus(201);
        expect($response->json("data.email"))->toBe("minimal@example.com");
    });



});

describe("GET /api/suppliers/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->supplier = \App\Models\Supplier::create([
            "name" => "John Doe",
            "company_name" => "PT Sumber Makmur",
            "email" => "john.doe@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Mandiri",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP123456",
            "nib_number" => "NIB987654",
            "business_type" => "Distributor",
            "note" => "Supplier utama untuk bahan baku"
        ]);
    });

    it("should get supplier detail by id successfully", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers/{$this->supplier->id}");

        $response->assertStatus(200);
        expect($response->json("data.name"))->toBe("John Doe");
        expect($response->json("data.email"))->toBe("john.doe@example.com");
        expect($response->json("data.company_name"))->toBe("PT Sumber Makmur");
    });

    it("should return 404 if supplier not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers/999999");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });

    it("should return 404 if supplier not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers/X-INVALID-ID");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });
});

describe("PATCH /api/suppliers/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->supplier = \App\Models\Supplier::create([
            "name" => "John Doe",
            "company_name" => "PT Sumber Makmur",
            "email" => "john.doe@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Mandiri",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP123456",
            "nib_number" => "NIB987654",
            "business_type" => "Distributor",
            "note" => "Supplier utama untuk bahan baku"
        ]);
    });

    it("Should successfully update existing supplier", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "name" => "Jane Doe",
            "company_name" => "PT Baru",
            "email" => "jane@example.com",
            "phone" => "0822222222",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Baru No. 10",
            "bank_account" => "999888777",
            "bank_name" => "Bank Baru",
            "npwp_number" => "09.876.543.2-109.876",
            "siup_number" => "SIUP2024",
            "nib_number" => "NIB2024",
            "business_type" => "Distributor",
            "note" => "Diupdate karena perubahan data"
        ];

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}", $payload);
        $response->assertStatus(200);
        expect($response->json("data.name"))->toBe("Jane Doe");

        $this->assertDatabaseHas("suppliers", [
            "id" => $this->supplier->id,
            "email" => "jane@example.com",
            "company_name" => "PT Baru",
            "note" => "Diupdate karena perubahan data"
        ]);
    });

    it("Should return 404 if supplier not found on update", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/suppliers/99999", [
            "name" => "Dummy",
        ]);

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });

    it("Should return 404 if supplier not found on update even tho if id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/suppliers/X-INVALID-ID", [
            "name" => "Dummy",
        ]);

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });

    it("Should update supplier address only", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "address" => "Jl. Sudirman No. 99"
        ];

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}", $payload);

        $response->assertStatus(200);
        expect($response->json("data.address"))->toBe("Jl. Sudirman No. 99");

        $this->assertDatabaseHas("suppliers", [
            "id" => $this->supplier->id,
            "address" => "Jl. Sudirman No. 99"
        ]);
    });
    it("Should update nullable fields like note and npwp_number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payload = [
            "note" => "Diperbarui untuk pengiriman cepat",
            "npwp_number" => "99.999.999.9-999.999"
        ];

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}", $payload);

        $response->assertStatus(200);
        expect($response->json("data.note"))->toBe("Diperbarui untuk pengiriman cepat");

        $this->assertDatabaseHas("suppliers", [
            "id" => $this->supplier->id,
            "note" => "Diperbarui untuk pengiriman cepat",
            "npwp_number" => "99.999.999.9-999.999"
        ]);
    });
    it("Should fail when updating email to one that already exists", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        \App\Models\Supplier::create([
            "name" => "Supplier Lain",
            "company_name" => "PT Lain",
            "email" => "lain@example.com",
            "phone" => "0800000000",
            "city" => "Bandung",
            "postal_code" => "40000",
            "province" => "Jawa Barat",
            "country" => "Indonesia",
            "address" => "Alamat",
            "bank_account" => "9876543210",
            "bank_name" => "Bank Lain",
            "business_type" => "Retail"
        ]);

        $payload = [
            "email" => "lain@example.com"
        ];

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}", $payload);

        $response->assertStatus(400);
        expect($response->json("errors.message.email.0"))->toBe("The email has already been taken.");
    });

});
describe("GET /api/suppliers", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        foreach (range(1, 50) as $i) {
            \App\Models\Supplier::create([
                "name" => "Supplier $i",
                "company_name" => "PT Perusahaan $i",
                "email" => "supplier$i@example.com",
                "phone" => "0812345678$i",
                "city" => $i % 2 == 0 ? "Bandung" : "Surabaya",
                "postal_code" => "1000$i",
                "province" => "Jawa Barat",
                "country" => $i % 2 == 0 ? "Indonesia" : "Malaysia",
                "address" => "Alamat $i",
                "bank_account" => "12345678$i",
                "bank_name" => "Bank ABC",
                "npwp_number" => null,
                "siup_number" => null,
                "nib_number" => null,
                "business_type" => "Pemasok",
                "note" => "Catatan untuk supplier $i"
            ]);
        }
    });

    it("should return paginated list of suppliers", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?per_page=10");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data",
            "message",
            "statusCode",
            "links",
            "meta"
        ]);

        expect(count($response->json("data")))->toBe(10);
        expect($response->json("message"))->toBe("Suppliers retrieved successfully");
    });

    it("should filter suppliers by name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?name=Supplier 1");

        $response->assertStatus(200);
        $data = $response->json("data");
        expect(collect($data)->pluck("name")->contains("Supplier 1"))->toBeTrue();
    });

    it("should filter suppliers by company_name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?company_name=Perusahaan 10");

        $response->assertStatus(200);
        $data = $response->json("data");

        expect(collect($data)->pluck("company_name")->first())->toContain("Perusahaan 10");
    });

    it("should filter suppliers by city and country", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?city=Bandung&country=Indonesia");

        $response->assertStatus(200);
        $data = $response->json("data");

        foreach ($data as $supplier) {
            expect($supplier["city"])->toBe("Bandung");
            expect($supplier["country"])->toBe("Indonesia");
        }
    });

    it("should apply multiple filters and paginate", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?city=Bandung&country=Indonesia&per_page=5");

        $response->assertStatus(200);
        expect(count($response->json("data")))->toBeLessThanOrEqual(5);

        foreach ($response->json("data") as $supplier) {
            expect($supplier["city"])->toBe("Bandung");
            expect($supplier["country"])->toBe("Indonesia");
        }
    });

    it("should return validation error when per_page is not a number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?per_page=lima");

        $response->assertStatus(422);

        expect($response->json("errors.message.per_page"))->toBe("The per_page parameter must be a positive integer.");
    });

    it("should return validation error when page is not a number", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?page=lima");

        $response->assertStatus(422);

        expect($response->json("errors.message.page"))->toBe("The page parameter must be a positive integer.");
    });
    it("should apply multiple filters and paginate test page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/suppliers?per_page=5&page=5");

        $response->assertStatus(200);
        expect(count($response->json("data")))->toBeLessThanOrEqual(5);
        expect($response->json("meta.current_page"))->toBe(5);
    });

});

describe("DELETE /api/suppliers/{id}", function () {
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

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->purchaseUser = \App\Models\User::query()->create([
            "name" => "supplier",
            "email" => "supplier@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
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

    });


    it('should soft delete supplier successfully if no related products', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $supplier = \App\Models\Supplier::create([
            'name' => 'No Product Supplier',
            'company_name' => 'Test Corp',
            'email' => 'noproduct@supplier.com',
            'phone' => '08123456789',
            'city' => 'Bandung',
            'postal_code' => '40251',
            'province' => 'Jawa Barat',
            'country' => 'Indonesia',
            'address' => 'Jl. Uji No. 1',
            'bank_account' => '9876543210',
            'bank_name' => 'Bank Test',
            'npwp_number' => '00.000.000.0-000.000',
            'siup_number' => 'SIUP000000',
            'nib_number' => 'NIB000000',
            'business_type' => 'Retail',
            'note' => 'No product attached'
        ]);

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}");
        dump($response->json());

        $response->assertStatus(200);
        expect(\App\Models\Supplier::withTrashed()->find($supplier->id))->not->toBeNull();
        expect(\App\Models\Supplier::find($supplier->id))->toBeNull();
    });


    it('should return 404 if supplier not found', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/suppliers/999999");

        $response->assertStatus(404);
    });it('should return 404 if supplier not found even id is invalid string', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/suppliers/X-INVALID-ID");

        $response->assertStatus(404);
    });
    it('should not allow unauthenticated user to delete supplier', function () {
        $response = $this->deleteJson("/api/suppliers/{$this->supplier->id}");

        $response->assertStatus(401);
    });

    it('should forbid user with Inventory role to delete supplier', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->inventory);

        $response = $this->deleteJson("/api/suppliers/{$this->supplier->id}");

        $response->assertStatus(403);
    });

    it('should forbid user with Finance role to delete supplier', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->finance);

        $response = $this->deleteJson("/api/suppliers/{$this->supplier->id}");

        $response->assertStatus(403);
    });

    it("Should be fail deleted because there is a relationship with products", function () {
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


        $response = $this->actingAs($this->purchaseUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)->deleteJson("/api/suppliers/{$this->supplier->id}");
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => "Cannot delete supplier because it is still associated with one or more purchases."
            ]
        ]);
    });


});




describe("PATCH /api/suppliers/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->supplier = \App\Models\Supplier::create([
            "name" => "John Doe",
            "company_name" => "PT Sumber Makmur",
            "email" => "john.doe@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Mandiri",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP123456",
            "nib_number" => "NIB987654",
            "business_type" => "Distributor",
            "note" => "Supplier utama untuk bahan baku"
        ]);
    });

    it("should successfully restore a soft deleted supplier", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->supplier->delete();

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}/restore");

        $response->assertStatus(200);
        expect($response->json("data.id"))->toBe($this->supplier->id);
        $this->assertDatabaseHas("suppliers", [
            "id" => $this->supplier->id,
            "deleted_at" => null
        ]);
    });

    it("should return 404 if supplier is not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $invalidId = 9999;

        $response = $this->patchJson("/api/suppliers/{$invalidId}/restore");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });

    it("should return 404 if supplier is not found even tho id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $invalidId = "X-INVALID-ID";

        $response = $this->patchJson("/api/suppliers/{$invalidId}/restore");

        $response->assertStatus(404);
        expect($response->json("errors.message"))->toBe("Supplier not found");
    });

    it("should return 400 if supplier is not soft deleted", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}/restore");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Supplier is not deleted, cannot be restored.");
    });
    it("should fail on second restore attempt", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->supplier->delete();

        $this->patchJson("/api/suppliers/{$this->supplier->id}/restore")->assertStatus(200);

        $response = $this->patchJson("/api/suppliers/{$this->supplier->id}/restore");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Supplier is not deleted, cannot be restored.");
    });

});

describe("DELETE /api/suppliers/{id}/force", function () {
    beforeEach(function () {
        $this->adminRole = Role::create(["name" => "Admin"]);
        $this->category = Category::create(["name" => "Kategori A"]);
        $this->unit = Unit::create(["name" => "Kilogram", "code" => "kg"]);

        $this->admin = User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        Sanctum::actingAs($this->admin);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->purchaseUser = \App\Models\User::query()->create([
            "name" => "supplier",
            "email" => "supplier@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);
    });
    it("should successfully force delete a soft deleted supplier with no products", function () {
        $supplier = Supplier::create([
            "name" => "Test Supplier",
            "company_name" => "PT Test",
            "email" => "test@example.com",
            "phone" => "0812345678",
            "city" => "Bandung",
            "postal_code" => "12345",
            "province" => "Jabar",
            "country" => "Indonesia",
            "address" => "Jl. Test No. 1",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Test",
            "npwp_number" => null,
            "siup_number" => null,
            "nib_number" => null,
            "business_type" => "Retail",
            "note" => null,
        ]);
        $supplier->delete();

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}/force");

        $response->assertStatus(200);

        $this->assertDatabaseMissing("suppliers", ["id" => $supplier->id]);
    });
    it("should return 404 if supplier not found at all", function () {
        $response = $this->deleteJson("/api/suppliers/999/force");

        $response->assertStatus(404);
        $response->assertJson([
            "errors" => [
                "message" => "Supplier not found"
            ]
        ]);
    });

    it("should return 404 if supplier not found at all even if id is invalid string", function () {
        $response = $this->deleteJson("/api/suppliers/X-INVALID-ID/force");

        $response->assertStatus(404);
        $response->assertJson([
            "errors" => [
                "message" => "Supplier not found"
            ]
        ]);
    });

    it("should return 400 if supplier exists but is not soft deleted", function () {
        $supplier = Supplier::create([
            "name" => "Not Deleted Supplier",
            "company_name" => "PT ABC",
            "email" => "abc@example.com",
            "phone" => "08123456789",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. ABC No. 123",
            "bank_account" => "9876543210",
            "bank_name" => "Bank ABC",
            "npwp_number" => null,
            "siup_number" => null,
            "nib_number" => null,
            "business_type" => "Distributor",
            "note" => null,
        ]);

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}/force");

        $response->assertStatus(400);
        $response->assertJson([
            "errors" => [
                "message" => "Supplier is not deleted, cannot be hard deleted."
            ]
        ]);
    });

    it("Should be fail deleted because there is a relationship with products", function () {
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

        $response = $this->actingAs($this->purchaseUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

        $this->supplier->delete();
        $response = $this->actingAs($this->admin)->deleteJson("/api/suppliers/{$this->supplier->id}/force");
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => "Cannot force delete supplier because it is still associated with one or more purchases."
            ]
        ]);
    });

});
