<?php

use App\Models\Customer;
use App\Models\Role;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/customers", function(){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(["name" => "Admin"]);

        $this->admin = \App\Models\User::create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    it("Should be success register new customer and return data", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "Budi Santoso",
            "company_name" => "PT Sukses Selalu",
            "email" => "budi@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10, Jakarta Pusat",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Central Asia",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP-123456789",
            "nib_number" => "NIB-987654321",
            "business_type" => "Distributor",
            "note" => "Customer tetap sejak 2022"
        ];

        $response = $this->postJson("/api/customers", $payLoad);
        expect($response)->status()->toBe(201);
        expect($response->json("data.name"))->toBe("Budi Santoso");
        expect($response->json("data.company_name"))->toBe("PT Sukses Selalu");
        expect($response->json("data.email"))->toBe("budi@example.com");
        expect($response->json("data.siup_number"))->toBe("SIUP-123456789");
        expect($response->json("data.nib_number"))->toBe("NIB-987654321");
        expect($response->json("data.business_type"))->toBe("Distributor");
        expect($response->json("data.note"))->toBe("Customer tetap sejak 2022");
        expect($response->json("data.bank_account"))->toBe("1234567890");
        expect($response->json("data.bank_name"))->toBe("Bank Central Asia");
        expect($response->json("data.npwp_number"))->toBe("12.345.678.9-012.345");
        expect($response->json("data.province"))->toBe("DKI Jakarta");
        expect($response->json("data.country"))->toBe("Indonesia");
        expect($response->json("data.address"))->toBe("Jl. Merdeka No. 10, Jakarta Pusat");
        expect($response->json("message"))->toBe("Customer created successfully");


    });

    it("Should be failed register new customer and return validation error", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $payLoad = [];

        $response = $this->postJson("/api/customers", $payLoad);
        expect($response)->status()->toBe(400);
        expect($response->json("errors.message.name.0"))->toBe("The name field is required.");
    });

    it("Should be failed register new customer because email already exists", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "Budi Santoso",
            "company_name" => "PT Sukses Selalu",
            "email" => "budi@example.com",
            "phone" => "081234567890",
            "city" => "Jakarta",
            "postal_code" => "12345",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "address" => "Jl. Merdeka No. 10, Jakarta Pusat",
            "bank_account" => "1234567890",
            "bank_name" => "Bank Central Asia",
            "npwp_number" => "12.345.678.9-012.345",
            "siup_number" => "SIUP-123456789",
            "nib_number" => "NIB-987654321",
            "business_type" => "Distributor",
            "note" => "Customer tetap sejak 2022"
        ];

        $this->postJson("/api/customers", $payLoad);
        $response = $this->postJson("/api/customers", $payLoad);

        expect($response)->status()->toBe(400);
        expect($response->json("errors.message.email.0"))->toBe("The email has already been taken.");
    });
});


describe("GET /api/customers/{id}", function(){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->create();
    });

    it("Should be success get customer by id", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/customers/" . $this->customer->id);
        expect($response)->status()->toBe(200);
        expect($response->json("data.name"))->toBe($this->customer->name);
        expect($response->json("data.company_name"))->toBe($this->customer->company_name);
        expect($response->json("data.email"))->toBe($this->customer->email);
        expect($response->json("data.siup_number"))->toBe($this->customer->siup_number);
        expect($response->json("data.nib_number"))->toBe($this->customer->nib_number);
        expect($response->json("data.business_type"))->toBe($this->customer->business_type);
    });

    it("Should be return 404 not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers/999999");
        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Customer not found");
    });

    it("Should be return 404 not found even id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers/X-INVALID-ID");
        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Customer not found");
    });
});

describe("PATCH /api/customers/{id}", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->create();
    });

    it("Should be success update customer", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $payLoad = [
            "name" => "CUSTOMER NAME UPDATED"
        ];

        $response = $this->patchJson("/api/customers/" . $this->customer->id, $payLoad);

        dump($response->json());
        expect($response->json("data.name"))->toBe("CUSTOMER NAME UPDATED");
        $this->assertDatabaseHas("customers", [
            "id" => $this->customer->id,
            "name" => "CUSTOMER NAME UPDATED"
        ]);

    });

    it("Should be failed update customer because id is invalid", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);
        $payLoad = [
            "name" => "CUSTOMER NAME UPDATED"
        ];
        $response = $this->patchJson("/api/customers/999", $payLoad);
        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Customer not found");
    });

    it("Should be failed update customer because id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);
        $payLoad = [
            "name" => "CUSTOMER NAME UPDATED"
        ];
        $response = $this->patchJson("/api/customers/X-INVALID-ID", $payLoad);
        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Customer not found");
    });

    it("Should be success update label customer", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $payLoad = [
            "name" => "CUSTOMER LABEL UPDATED",
            "company_name" => "CUSTOMER LABEL UPDATED",
            "email" => "updated@example.com",
        ];
        $response = $this->patchJson("/api/customers/" . $this->customer->id, $payLoad);
        expect($response->json("data.label"))->toBe("CUSTOMER LABEL UPDATED - CUSTOMER LABEL UPDATED (updated@example.com)");;
    });
});


describe("GET /api/customers", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->count(50)->create();
    });

    it("Should be success get all customers", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers");
        expect($response)->status()->toBe(200);
        expect($response->json("data.0.name"))->toBe($this->customer[0]->name);
        expect($response->json("data"))->toHaveCount(10);
        expect($response->json("message"))->toBe("Customer retrieved successfully");

    });

    it("Should be success get all customers with paging", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers?page=2&per_page=20");
        expect($response)->status()->toBe(200);
        expect($response->json("data.0.name"))->toBe($this->customer[20]->name);
        expect($response->json("data"))->toHaveCount(20);
        expect($response->json("message"))->toBe("Customer retrieved successfully");

    });

    it("Should be success get all customers with filters", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $targetCustomer = $this->customer[0];
        $targetCustomer->update([
            'name' => 'Joko Widodo',
            'company_name' => 'PT Maju Mundur',
            'city' => 'Bandung',
            'country' => 'Indonesia',
        ]);

        $response = $this->get("/api/customers?city=Bandung&name=joko&company_name=maju&country=ind&toll=xinvalid&page=satu");
        expect($response)->status()->toBe(200);
        expect($response->json("data"))->toHaveCount(1);
        expect($response->json("message"))->toBe("Customer retrieved successfully");

    });

});

describe("DELETE /api/customers/{id}", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->create();
    });

    it("Should be success delete customer by id", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->deleteJson("/api/customers/{$this->customer->id}");
        $response->assertOk();
        expect($response->json("data.message"))->toBe("Customer deleted successfully");
        $this->assertSoftDeleted("customers", ["id" => $this->customer->id]);


    });

    it("Should be return not found", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/customers/9999");

        expect($response->json("errors.message"))->toBe("Customer not found");
    });

    it("Should be return not found even invalid id string", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/customers/x-invalid-id");

        expect($response->json("errors.message"))->toBe("Customer not found");
    });
});

describe("PATCH /api/customers/{id}/restore", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->create();
    });

    it("Should be success restore deleted customer", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->customer->delete();
        $response = $this->patchJson("/api/customers/{$this->customer->id}/restore");

        expect($response->json("message"))->toBe("Customer restored successfully");
        expect($response->json("data.name"))->toBe($this->customer->name);
        $response->assertOk();
    });

    it("Should be fail restore customer because customer is not deleted", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response= $this->patchJson("/api/customers/{$this->customer->id}/restore");
        expect($response->json("errors.message"))->toBe("Cannot restore because customer is not deleted");
        expect($response)->status()->toBe(400);
    });

    it("Should be fail restore customer because customer id is not found", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response= $this->patchJson("/api/customers/999/restore");
        expect($response->json("errors.message"))->toBe("Customer not found");
        expect($response)->status()->toBe(404);
    });

    it("Should be fail restore customer because customer id is not found even tho id is invalid string", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response= $this->patchJson("/api/customers/x-invalid-id/restore");
        expect($response->json("errors.message"))->toBe("Customer not found");
        expect($response)->status()->toBe(404);
    });
});


describe("DELETE /api/customers/{id}/force", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->create();
    });

    it("Should be success force delete customer", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->customer->delete();
        $response = $this->deleteJson("/api/customers/{$this->customer->id}/force");
        expect($response->json("data.message"))->toBe("Customer deleted successfully");
        $response->assertOk();
    });

    it("Should be fail force delete customer because customer is not deleted", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/customers/{$this->customer->id}/force");
        expect($response->json("errors.message"))->toBe("Cannot hard delete because customer is not deleted");
        expect($response)->status()->toBe(400);
    });

    it("Should be fail force delete customer because customer id is not found", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/customers/9999/force");
        expect($response->json("errors.message"))->toBe("Customer not found");
        expect($response)->status()->toBe(404);
    });

    it("Should be fail force delete customer because customer id is not found even tho id is invalid string", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/customers/X-INVALID-ID/force");
        expect($response->json("errors.message"))->toBe("Customer not found");
        expect($response)->status()->toBe(404);
    });
});


describe("GET /api/customers/trashed", function (){
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

        $this->customer = Customer::factory()->count(50)->create();

        $this->customer->each(function ($customer){
            $customer->delete();
        });
    });

    it("Should be success get all trashed customers", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers/trashed");
        expect($response)->status()->toBe(200);
        expect($response->json("data.0.name"))->toBe($this->customer[0]->name);
        expect($response->json("data"))->toHaveCount(10);
        expect($response->json("message"))->toBe("Customer retrieved successfully");
    });

    it("Should be success get all trashed customers with paging", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $response = $this->get("/api/customers/trashed?per_page=50");
        expect($response)->status()->toBe(200);
        expect($response->json("data.0.name"))->toBe($this->customer[0]->name);
        expect($response->json("data"))->toHaveCount(50);
        expect($response->json("message"))->toBe("Customer retrieved successfully");
    });
});
