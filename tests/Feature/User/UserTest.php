<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("GET /api/users/current", function () {
    beforeEach(function () {
        $this->user = \App\Models\User::query()->create([
            "email" => "jonathan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "name" => "Jonathan Gunawan",
            "status" => "active"
        ]);
    });

    it("Should be success get users current", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->get("/api/users/current", [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json'
        ]);

        expect($response)->status()->toBe(200);
    });

    it("Should be failed get users current", function () {
        $invalidToken = "invalid-token";

        $response = $this->get("/api/users/current", [
            'Authorization' => "Bearer $invalidToken",
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(401);
    });
});

describe("PATCH /api/users/current", function () {
    beforeEach(function () {
        $this->user = \App\Models\User::query()->create([
            "email" => "jonathan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "name" => "Jonathan Gunawan",
            "status" => "active"
        ]);
    });

    it("Should be success update user data", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [
            "name" => "Jonathan Gunawan UPDATED"
        ], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        expect($response)->status()->toBe(200);
    });

    it("Should be failed update user data", function () {

        $token = "X-WRONG-TOKEN";

        $response = $this->patch("/api/users/current", [
            "name" => "Jonathan Gunawan UPDATED"
        ], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        expect($response)->status()->toBe(401);
    });

    it("Should be success update user data even tho request is empty", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        expect($response)->status()->toBe(200);
    });

    it("Should be success update user data even tho field is unique", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [
            "email" => "jonathanupdated@example.com"
        ], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        expect($response)->status()->toBe(200);
    });

    it("Should be success update user data even if the request contains the same data", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [
            "email" => "jonathan@example.com"
        ], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        expect($response)->status()->toBe(200);
    });

    it("Should be failed update user data cuz unique data already exists", function () {

        \App\Models\User::query()->create([
            "email" => "gunawan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "name" => "Gunawan",
            "status" => "active"
        ]);

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [
            "email" => "gunawan@example.com"
        ], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);


        expect($response)->status()->toBe(400);
    });
});

describe("DELETE /api/users/logout", function (){
    beforeEach(function () {
        $this->user = \App\Models\User::query()->create([
            "email" => "jonathan@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "name" => "Jonathan Gunawan",
            "status" => "active"
        ]);
    });

   it("Should be success logged out current user", function (){
       $token = $this->user->createToken('X-API-Token')->plainTextToken;

       $response = $this->delete("/api/users/logout",[] ,[
           "Authorization" => "Bearer $token",
           "Accept" => "application/json"
       ]);

       expect($response)->status()->toBe(200);
   });

    it("Should be failed logged out current user and return unauthorized", function (){
        $token = "X-WRONG-TOKEN";

        $response = $this->delete("/api/users/logout",[] ,[
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);


        expect($response)->status()->toBe(401);
    });
});

describe("PATCH /api/admin/users/{id}/status", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(['name' => 'Admin']);
        $this->userRole = \App\Models\Role::create(['name' => 'User']);

        $this->admin = \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        $this->user = \App\Models\User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role_id' => $this->userRole->id,
            'status' => 'inactive'
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });
    it("Should be successfully update user status", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/status", [
            'status' => 'active'
        ]);



        expect($response)->status()->toBe(200);
        expect($response->json('data.message'))->toBe('User status updated successfully.');
        expect($response->json('data.user.status'))->toBe('active');

        \Pest\Laravel\assertDatabaseHas('users', [
            'id' => $this->user->id,
            'status' => 'active'
        ]);
    });

    it("Should fail if status is missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/status", []);



        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.status.0'))->toBe('The status field is required.');
    });

    it("Should fail if status value is invalid", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/status", [
            'status' => 'pending'
        ]);



        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.status.0'))->toBe('The selected status is invalid.');
    });

    it("Should fail if user not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/9999/status", [
            'status' => 'inactive'
        ]);

        expect($response)->status()->toBe(404);
        expect($response->json('errors.message'))->toBe('User not found');
    });

    it("Should return 401 if unauthenticated", function () {
        $response = $this->patchJson("/api/admin/users/{$this->user->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(401);
    });

    it("Should forbid non-admin from updating user status", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->patchJson("/api/admin/users/{$this->admin->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(403);
    });

    it("Should not allow user to deactivate themselves if only admin", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->admin->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(400);
        expect($response->json('errors.message'))->toBe('You cannot deactivate your own account.');
    });


    it("Should not allow admin to deactivate themselves even if other admins exist", function () {
        $admin2 = \App\Models\User::create([
            'name' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($admin2);

        $response = $this->patchJson("/api/admin/users/{$admin2->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(400);
        expect($response->json('errors.message'))->toBe('You cannot deactivate your own account.');
    });

    it("Should allow admin to activate another inactive admin", function () {
        $admin2 = \App\Models\User::create([
            'name' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id,
            'status' => 'inactive'
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$admin2->id}/status", [
            'status' => 'active'
        ]);


        expect($response)->status()->toBe(200);
        expect($response->json('data.message'))->toBe('User status updated successfully.');
        expect($response->json('data.user.status'))->toBe('active');
    });

    it("Should allow admin to deactivate another admin if other active admins remain", function () {
        $admin2 = \App\Models\User::create([
            'name' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id,
            'status' => 'active'
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$admin2->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(200);
        expect($response->json('data.user.status'))->toBe('inactive');
    });

    it("Should not allow admin to deactivate another admin if only one active admin left", function () {
        $inactiveAdmin = \App\Models\User::create([
            'name' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id,
            'status' => 'inactive'
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->admin->id}/status", [
            'status' => 'inactive'
        ]);


        expect($response)->status()->toBe(400);
        expect($response->json('errors.message'))->toBe('You cannot deactivate your own account.');
    });

});


