<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/admin/users", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "user"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successfully register new user", function () {

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/admin/users", [
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => "rahasia12345",
            "password_confirmation" => "rahasia12345",
            "role_id" => $this->userRole->id
        ]);

        dump($response->json());
        expect($response)->status()->toBe(201);
    });

    it("Should be failed register new user and return forbidden", function () {
        $user = \App\Models\User::query()->create([
            "name" => "gunawan",
            "email" => "gunawan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($user);

        $response = $this->post("/api/admin/users", [
            "name" => "Natha",
            "email" => "nath@gmail.com",
            "password" => "rahasia12345",
            "password_confirmation" => "rahasia12345",
            "role_id" => $this->userRole->id
        ]);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });
});

describe("GET /api/admin/users/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "user"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });
    it("Should be success get user by id", function () {

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/users/" . $this->user->id);

        dump($response->json());
        expect($response)->status()->toBe(200);
    });

    it("Should be failed get user by id", function () {

        $user = \App\Models\User::query()->create([
            "name" => "gunawan",
            "email" => "gunawan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->get("/api/admin/users/" . $user->id);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed get user by id not found", function () {

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/users/398475928347592");

        dump($response->json());
        expect($response)->status()->toBe(404);
    });
});

describe("PATCH /api/admin/users/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "user"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);
    });
    it("Should be success update user by id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patch("/api/admin/users/" . $this->user->id, [
            "email" => "emailupdated@example.com"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(200);
    });

    it("Should be failed update user by id and return forbidden 403", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->patch("/api/admin/users/" . $this->user->id, [
            "email" => "emailupdated@example.com"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed update user by id and return not found 404", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patch("/api/admin/users/" . $this->user->id * 2, [
            "email" => "emailupdated@example.com"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(404);
    });
});

describe("DELETE /api/admin/users/{id}", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "user"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);
    });

    it("Should be success delete user data", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/admin/users/" . $this->user->id);

        dump($response->json());
        expect($response)->status()->toBe(200);
    });

    it("Should be failed delete user data and return forbidden 403", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->delete("/api/admin/users/" . $this->user->id);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed delete user data and return not found 404", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/admin/users/" . $this->user->id * 2);

        dump($response->json());
        expect($response)->status()->toBe(404);
    });
    it("Should fail to delete self and return 400", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/admin/users/" . $this->admin->id);

        dump($response->json());

        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.0'))->toBe("You cannot delete your own account.");
    });

});

describe("PATCH /api/admin/users/{id}/restore", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "User"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);

        $this->user->delete();
    });

    it("Should be success restore deleted user", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patch("/api/admin/users/{$this->user->id}/restore");

        dump($response->json());

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    "message" => "User restored successfully"
                ]
            ]);

        expect(\App\Models\User::withTrashed()->find($this->user->id)->deleted_at)->toBeNull();
    });

    it("Should be failed restore deleted user and return forbidden 403", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->patch("/api/admin/users/{$this->user->id}/restore");

        dump($response->json());

        $response->assertStatus(403)
            ->assertJson([
                "errors" => ["message" => "Forbidden"]
            ]);
    });

    it("Should be failed restore deleted user and return not found 404", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patch("/api/admin/users/9999/restore");

        dump($response->json());

        $response->assertStatus(404)
            ->assertJson([
                "errors" => ["message" => "User not found"]
            ]);
    });
});


describe("DELETE /api/admin/users/{id}/force", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "User"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);

        $this->user->delete();
    });

    it("Should be success force delete user", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/admin/users/{$this->user->id}/force");

        dump($response->json());

        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    "message" => "User permanently deleted"
                ]
            ]);

        expect(\App\Models\User::withTrashed()->find($this->user->id))->toBeNull();
    });

    it("Should be failed force delete user and return forbidden 403", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->delete("/api/admin/users/{$this->user->id}/force");

        dump($response->json());

        $response->assertStatus(403)
            ->assertJson([
                "errors" => ["message" => "Forbidden"]
            ]);
    });

    it("Should be failed force delete user and return not found 404", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete("/api/admin/users/9999/force");

        dump($response->json());

        $response->assertStatus(404)
            ->assertJson([
                "errors" => ["message" => "User not found"]
            ]);
    });
});

describe("GET /api/admin/users", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);
        $this->userRole = \App\Models\Role::query()->create([
            "name" => "User"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);

    });

    it("Should be success get user list data", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/users");

        dump($response->json());
        expect($response)->status()->toBe(200);

    });

    it("Should return filtered user list by role_id", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/users?role_id={$this->userRole->id}&per_page=10");

        dump($response->json());
        $response->assertStatus(200);
        expect($response)->status()->toBe(200);
    });

    it("Should return filtered user list by search query", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $user = \App\Models\User::create([
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => "rahasia12345",
            "role_id" => $this->userRole->id
        ]);

        $response = $this->get("/api/admin/users?search=Jonathan");
        dump($response->json());
        $response->assertStatus(200);
        expect(collect($response->json("data"))->pluck("id"))->toContain($user->id);
    });

});
