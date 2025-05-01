<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe("POST /api/couriers", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);

        $this->salesRole = Role::factory()->create(["name" =>"Sales"]);
        $this->sales = User::factory()->create([
            "role_id" => $this->salesRole->id,
        ]);

    });
    it("Should be success register new courier", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "Pak Mamat",
            "phone" => "081234567890",
            "status" => "available"
        ];

        $response = $this->postJson("/api/couriers", $payLoad);
        $response->assertCreated();
        expect($response->json("data.name"))->toBe("Pak Mamat");
        expect($response->json("data.phone"))->toBe("081234567890");
        expect($response->json("data.status"))->toBe("available");
        expect($response->json("message"))->toBe("Courier created successfully");

        $this->assertDatabaseHas("couriers", [
            "name" => "Pak Mamat",
            "phone" => "081234567890",
            "status" => "available"
        ]);
    });

    it("Should be failed register new courier because phone is invalid", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);

        $payLoad = [
            "name" => "Pak Mamat",
            "phone" => "081234567890",
            "status" => "available"
        ];

        $this->postJson("/api/couriers", $payLoad);
        $response = $this->postJson("/api/couriers", $payLoad);
        $response->assertStatus(400);
        expect($response->json("errors.message.phone.0"))->toBe("The phone has already been taken.");
    });

    it("Should be failed register new courier because field is empty", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->sales);
        $payLoad = [
            "name" => "",
            "phone" => "",
            "status" => ""
        ];

        $response = $this->postJson("/api/couriers", $payLoad);
        $response->assertStatus(400);
        expect($response->json("errors.message.name.0"))->toBe("The name field is required.");
        expect($response->json("errors.message.phone.0"))->toBe("The phone field is required.");
        expect($response->json("errors.message.status.0"))->toBe("The status field is required.");
    });
});

describe("GET /api/couriers/{id}", function () {
    beforeEach(function () {
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
    });

   it("Should be success get courier by id", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);
       $courier = \App\Models\Courier::factory()->create();
       $response = $this->get("/api/couriers/" . $courier->id);
       $response->assertOk();
       expect($response->json("data.name"))->toBe($courier->name);
       expect($response->json("data.phone"))->toBe($courier->phone);
       expect($response->json("data.status"))->toBe($courier->status);
       expect($response->json("message"))->toBe("Courier retrieved successfully");
       expect($response->json("data.id"))->toBe($courier->id);

   });

   it("Should be return 404 not found", function () {
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);
       $response = $this->get("/api/couriers/999999");
       $response->assertNotFound();
       expect($response->json("errors.message"))->toBe("Courier not found");
   });


    it("Should be return 404 not found even id is invalid string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->get("/api/couriers/X-INVALID-ID");;
        $response->assertNotFound();
        expect($response->json("errors.message"))->toBe("Courier not found");
    });
});

describe("PATCH /api/couriers/{id}", function (){
   beforeEach(function (){
       $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
       $this->admin = User::factory()->create([
           "role_id" => $this->adminRole->id,
       ]);
       $this->courier = \App\Models\Courier::factory()->create();
   });

   it("Should be success update existing courier", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [
            "name" => "Pak Mamat UPDATED",
            "phone" => "089876544321",
            "status" => "unavailable"
        ];
        $response = $this->patchJson("/api/couriers/{$this->courier->id}", $payLoad);
        expect($response->json("data.name"))->toBe("Pak Mamat UPDATED");
        expect($response->json("data.phone"))->toBe("089876544321");
        expect($response->json("data.status"))->toBe("unavailable");
   });

    it("Should be success update existing courier even tho payload is null", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [];
        $response = $this->patchJson("/api/couriers/{$this->courier->id}", $payLoad);
        $response->assertOk();
    });

    it("Should be success update existing courier even tho payload is only one", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = ["name" => "Pak Yanto Magang"];
        $response = $this->patchJson("/api/couriers/{$this->courier->id}", $payLoad);
        $response->assertOk();
        expect($response->json("data.name"))->toBe("Pak Yanto Magang");
    });

    it("Should be fail update if id is not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [];
        $response = $this->patchJson("/api/couriers/99999", $payLoad);
        $response->assertNotFound();
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Should be fail update if id is not found even tho id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $payLoad = [];
        $response = $this->patchJson("/api/couriers/X-INVALID-ID", $payLoad);
        $response->assertNotFound();
        expect($response->json("errors.message"))->toBe("Courier not found");
    });
});

describe("GET /api/couriers", function (){
    beforeEach(function (){
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
        $this->courier = \App\Models\Courier::factory()->count(50)->create();
    });

    it("Should be success get all coriers", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/couriers");
        expect(count($response->json("data")))->toBe(10);
    });

    it("Should be success get all coriers with paging", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/couriers?per_page=50");
        expect(count($response->json("data")))->toBe(50);
    });

});

describe("DELETE /api/couriers/{id}", function (){
    beforeEach(function (){
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
        $this->courier = \App\Models\Courier::factory()->create();
    });

    it("Should be success delete courier", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/" . $this->courier->id);
        $response->assertOk();
        expect($response->json("data.message"))->toBe("Courier deleted successfully");
    });

    it("Should be fail delete courier because id is not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/999999");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Should be fail delete courier because id is not found even tho id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/X-INVALID-ID");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });


});

describe("PATCH /api/couriers/{id}/restore", function (){
    beforeEach(function (){
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
        $this->courier = \App\Models\Courier::factory()->create();
    });

    it("Should be success restore courier", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->courier->delete();
        $response = $this->patchJson("/api/couriers/{$this->courier->id}/restore");
        $response->assertOk();
        expect($response->json("message"))->toBe("Courier restored successfully");
        expect($response->json("data.id"))->toBe($this->courier->id);
    });

    it("Should be fail restore courier because id is not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->patchJson("/api/couriers/999999/restore");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Should be fail restore courier because id is not found even tho id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->patchJson("/api/couriers/X-INVALID-ID/restore");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Shoule be fail restore courier because courier is not deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/couriers/{$this->courier->id}/restore");
        expect($response->json("errors.message"))->toBe("Cannot restore, courier is not deleted");
    });
});

describe("DELETE /api/couriers/{id}/force", function (){
    beforeEach(function (){
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
        $this->courier = \App\Models\Courier::factory()->create();
    });

    it("Should be success force delete courier", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $this->courier->delete();
        $response = $this->deleteJson("/api/couriers/{$this->courier->id}/force");
        $response->assertOk();
        expect($response->json("data.message"))->toBe("Courier deleted successfully");
    });

    it("Should be fail force delete courier because id is not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/999999/force");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Should be fail force delete courier because id is not found even tho id is invalid string", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/X-INVALID-ID/force");
        expect($response->json("errors.message"))->toBe("Courier not found");
    });

    it("Should be fail because courier is not deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->deleteJson("/api/couriers/{$this->courier->id}/force");
        expect($response->json("errors.message"))->toBe("Cannot hard delete, courier is not deleted");
    });
});

describe("GET /api/couriers/trashed", function (){
    beforeEach(function (){
        $this->adminRole = Role::factory()->create(["name" =>"Admin"]);
        $this->admin = User::factory()->create([
            "role_id" => $this->adminRole->id,
        ]);
        $this->courier = \App\Models\Courier::factory()->count(50)->create();
        foreach ($this->courier as $courier) {
            $courier->delete();
        }
    });

    it("Should be success get all couriers that is deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->getJson("/api/couriers/trashed");
        expect(count($response->json("data")))->toBe(10);
    });

    it("Should be success get all couriers that is deleted with paging", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->getJson("/api/couriers/trashed?per_page=50");
        expect(count($response->json("data")))->toBe(50);
        expect($response->json("data.0.id"))->toBe($this->courier[0]->id);
    });


});
