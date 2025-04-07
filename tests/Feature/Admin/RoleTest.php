<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
describe("POST /api/admin/roles", function (){
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

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

   it("Should be successfully create new role", function (){
         \Laravel\Sanctum\Sanctum::actingAs($this->admin);

         $response = $this->post("/api/admin/roles", [
             "name" => "Inventory"
         ]);

         dump($response->json());
         expect($response)->status()->toBe(201);
         \Pest\Laravel\assertDatabaseHas("roles", ["name" => "Inventory"]);
   });

    it("Should be failed create new role and return forbidden", function (){

        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->post("/api/admin/roles", [
            "name" => "Inventory"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed create new role and return unauthorized", function () {

        $response = $this->post("/api/admin/roles", [
            "name" => "Inventory"
        ], [
            "Accept" => "application/json"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(401);
    });

    it("Should fail to create a role with duplicate name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->post("/api/admin/roles", [
            "name" => "Inventory"
        ]);

        $response = $this->post("/api/admin/roles", [
            "name" => "Inventory"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
    });

    it("Should fail to create a role with empty name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->post("/api/admin/roles", [
            "name" => ""
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
        expect($response->json())->toHaveKeys(["errors"]);
    });

});

describe("GET /api/admin/roles/{id}", function (){
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

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be success find role by id", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $response = $this->get("/api/admin/roles/" . $this->userRole->id);

       dump($response->json());
       expect($response)->status()->toBe(200);

    });

    it("Should be failed find role by id and return not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/roles/" . $this->userRole->id * 10);

        dump($response->json());
        expect($response)->status()->toBe(404);

    });

    it("Should be failed find role by id and return forbidden", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->get("/api/admin/roles/" . $this->userRole->id);

        dump($response->json());
        expect($response)->status()->toBe(403);

    });
});

describe("PATCH /api/admin/roles/{id}", function (){
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

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

   it("Should be success update role", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);
       $response = $this->patch('/api/admin/roles/'. $this->userRole->id, [
           "name" => "UserRoleUPDATED"
       ]);

       dump($response->json());
       expect($response)->status()->toBe(200);
       \Pest\Laravel\assertDatabaseHas("roles", [
           "id" => $this->userRole->id,
           "name" => "UserRoleUPDATED"
       ]);
   });

    it("Should be failed update role and return required field error", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->patch('/api/admin/roles/'. $this->userRole->id, [
            "name" => ""
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
    });

    it("Should be failed update role and return forbidden", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->user);
        $response = $this->patch('/api/admin/roles/'. $this->userRole->id, [
            "name" => "UserRoleUPDATED"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed update role and return not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->patch('/api/admin/roles/'. $this->userRole->id * 10, [
            "name" => "UserRoleUPDATED"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(404);
    });
});

describe("DELETE /api/admin/roles/{id}", function (){
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

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

   it("Should be success soft delete role by id", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);
       $this->user->delete();

       $response = $this->delete('/api/admin/roles/' . $this->userRole->id);

       dump($response->json());
       expect($response)->status()->toBe(200);

       $response = $this->delete('/api/admin/roles/' . $this->userRole->id);
       expect($response)->status()->toBe(404);
   });

    it("Should be failed soft delete role by id and return 404 not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete('/api/admin/roles/' . $this->userRole->id * 10);

        dump($response->json());
        expect($response)->status()->toBe(404);
    });

    it("Should be failed soft delete role by id and return forbidden", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->delete('/api/admin/roles/' . $this->userRole->id * 10);

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should be failed soft delete role by id and return unauthorized", function (){

        $response = $this->delete('/api/admin/roles/' . $this->userRole->id, [], [
            "Accept" => "application/json"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(401);
    });

    it("Should be failed soft delete role by id and return cannot delete admin role", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);
        $response = $this->delete('/api/admin/roles/' . $this->adminRole->id, [], [
            "Accept" => "application/json"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
    });

    it("Should be failed soft delete role by id and return error because there are user assigned to this role", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->delete('/api/admin/roles/' . $this->userRole->id);

        dump($response->json());
        expect($response)->status()->toBe(400);

    });
});


describe("PATCH /api/admin/roles/{id}/restore", function (){

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

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

   it("Should be successfully restored role", function (){
       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $role = \App\Models\Role::find($this->userRole->id);
       $role->delete();

       $response = $this->patch("/api/admin/roles/" . $this->userRole->id. "/restore");

       dump($response->json());

       expect($response)->status()->toBe(200);
   });

    it("Should be failed restored role and return not found", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $role = \App\Models\Role::find($this->userRole->id);
        $role->delete();

        $response = $this->patch("/api/admin/roles/" . $this->userRole->id * 10 . "/restore");

        dump($response->json());

        expect($response)->status()->toBe(404);
    });

    it("Should be failed restored role and return forbidden", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $role = \App\Models\Role::find($this->adminRole->id);
        $role->delete();

        $response = $this->patch("/api/admin/roles/" . $this->adminRole->id. "/restore");

        dump($response->json());

        expect($response)->status()->toBe(403);
    });

    it("Should be failed restored role and return role not is not deleted", function (){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patch("/api/admin/roles/" . $this->userRole->id . "/restore");

        dump($response->json());

        expect($response)->status()->toBe(400);
    });
});


describe("DELETE /api/admin/roles/{id}/force", function (){

    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->userRole = \App\Models\Role::query()->create([
            "name" => "User"
        ]);

    });

    afterEach(function (){
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

   it("Should be successfully force delete role by id", function (){
       $this->admin = \App\Models\User::query()->create([
           "name" => "admin",
           "email" => "admin@example.com",
           "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
           "role_id" => $this->adminRole->id
       ]);

       \Laravel\Sanctum\Sanctum::actingAs($this->admin);

       $role = \App\Models\Role::find($this->userRole->id);
       $role->delete();

       $response = $this->delete("/api/admin/roles/" . $this->userRole->id . "/force");

       dump($response->json());

       expect($response)->status()->toBe(200);
   });

    it("Should be failed force delete role by id and return not found", function (){
        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $role = \App\Models\Role::find($this->userRole->id);
        $role->delete();

        $response = $this->delete("/api/admin/roles/" . $this->userRole->id * 10 . "/force");

        dump($response->json());

        expect($response)->status()->toBe(404);
    });

    it("Should be failed force delete role by id and return forbidden", function (){
        $this->user = \App\Models\User::query()->create([
            "name" => "user",
            "email" => "user@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->userRole->id
        ]);


        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $role = \App\Models\Role::find($this->adminRole->id);
        $role->delete();

        $response = $this->delete("/api/admin/roles/" . $this->adminRole->id . "/force");

        dump($response->json());

        expect($response)->status()->toBe(403);
    });

    it("Should be failed force delete role by id role and return cannot delete admin role", function (){
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
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $role = \App\Models\Role::find($this->adminRole->id);
        $this->delete("/api/admin/roles/" . $this->adminRole->id);

        $response = $this->delete("/api/admin/roles/" . $this->adminRole->id . "/force");

        dump($response->json());

        expect($response)->status()->toBe(400);
    });

    it("Should be failed force delete role by id and return cant delete because there are user assign to this role", function (){
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
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $role = \App\Models\Role::find($this->userRole->id);
        $role->delete();

        $response = $this->delete("/api/admin/roles/" . $this->userRole->id . "/force");

        dump($response->json());

        expect($response)->status()->toBe(400);
    });

});

describe("GET /api/admin/roles", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create(["name" => "Admin"]);
        $this->userRole = \App\Models\Role::query()->create(["name" => "User"]);

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

        foreach (range(1, 50) as $i) {
            \App\Models\Role::query()->create([
                "name" => "Role - $i"
            ]);
        }
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successfully get roles list as admin", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles");

        dump($response->json());
        expect($response)->status()->toBe(200);
        expect($response->json("data"))->toBeArray();
    });

    it("Should return unauthorized when not logged in", function () {
        $response = $this->getJson("/api/admin/roles");

        dump($response->json());
        expect($response)->status()->toBe(401);
    });

    it("Should return forbidden when logged in as non-admin", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/admin/roles");

        dump($response->json());
        expect($response)->status()->toBe(403);
    });

    it("Should support pagination with per_page param", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles?per_page=10");

        dump($response->json());
        expect($response)->status()->toBe(200);
        expect($response->json("meta.per_page"))->toBe(10);
        expect($response->json("data"))->toHaveCount(10);
    });

    it("Should support search by name", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles?search=Role - 2");

        dump($response->json());
        expect($response)->status()->toBe(200);

        $results = $response->json("data");
        expect($results)->not->toBeEmpty();
        foreach ($results as $role) {
            expect($role["name"])->toContain("Role - 2");
        }
    });

    it("Should return default paginated roles list (10 per page)", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/roles");
        $response->assertStatus(200);

        $json = $response->json();
        expect($json["data"])->toHaveCount(10);
    });

    it("Should return paginated roles list with custom per_page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/roles?per_page=25");
        $response->assertStatus(200);

        $json = $response->json();
        expect($json["data"])->toHaveCount(25);
    });

    it("Should return paginated roles list for page 2", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/roles?page=2");
        $json = $response->json();

        expect($response)->status()->toBe(200);
        expect($json["meta"]["current_page"] ?? $json["current_page"])->toBe(2);
    });

    it("Should return correct roles list with per_page and page query combined", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->get("/api/admin/roles?per_page=10&page=3");
        $json = $response->json();

        expect($response)->status()->toBe(200);
        expect($json["meta"]["current_page"] ?? $json["current_page"])->toBe(3);
        expect($json["data"])->toHaveCount(10);
    });

});

describe("GET /api/admin/roles/{id}/users", function (){
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::query()->create(["name" => "Admin"]);
        $this->userRole = \App\Models\Role::query()->create(["name" => "User"]);

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

        foreach (range(1, 50) as $i) {
            \App\Models\user::query()->create([
                "name" => "User - $i",
                "email" => "user" . $i . "@example.com",
                "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
                "role_id" => $this->userRole->id
            ]);
        }
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should be successful get users by specific role", function(){
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles/" . $this->userRole->id . "/users");

        $json = $response->json();
        dump($response->json());
        expect($response)->status()->toBe(200);
        expect($json["data"])->toHaveCount(10);
        expect($json["meta"]["current_page"] ?? $json["current_page"])->toBe(1);
    });

    it("Should return unauthorized if not logged in", function () {
        $response = $this->getJson("/api/admin/roles/" . $this->userRole->id . "/users");

        expect($response)->status()->toBe(401);
    });

    it("Should return forbidden for non-admin user", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/admin/roles/" . $this->userRole->id . "/users");

        expect($response)->status()->toBe(403);
    });

    it("Should return 404 if role id not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $invalidRoleId = 9999;
        $response = $this->getJson("/api/admin/roles/{$invalidRoleId}/users");

        dump($response->json());

        expect($response)->status()->toBe(404);
        expect($response->json("errors.message"))->toBe("Role not found");
    });

    it("Should return correct users with pagination and page param", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles/{$this->userRole->id}/users?page=2&per_page=15");

        $json = $response->json();

        expect($response)->status()->toBe(200);
        expect($json["data"])->toHaveCount(15);
        expect($json["meta"]["current_page"] ?? $json["current_page"])->toBe(2);
    });

    it("Should return filtered users by search query", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles/{$this->userRole->id}/users?search=User - 2");

        $json = $response->json();

        expect($response)->status()->toBe(200);
        expect(collect($json["data"])->pluck("name")->implode(", "))->toContain("User - 2");
    });

    it("Should return empty data when search not match", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles/{$this->userRole->id}/users?search=tidak-ada");

        $json = $response->json();

        expect($response)->status()->toBe(200);
        expect($json["data"])->toBe([]);
    });
    it("Should filter users by email keyword", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $email = "user10@example.com";
        $response = $this->getJson("/api/admin/roles/{$this->userRole->id}/users?search={$email}");

        dump($response->json());

        expect($response)->status()->toBe(200);
        expect($response->json("data"))->toHaveCount(1);
        expect($response->json("data.0.email"))->toBe($email);
    });

    it("Should filter users by email domain with custom per_page", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/admin/roles/{$this->userRole->id}/users?search=@example.com&per_page=25");

        dump($response->json());

        expect($response)->status()->toBe(200);
        expect($response->json("data"))->toHaveCount(25);
        expect($response->json("meta.total") ?? $response->json("total"))->toBe(51);
    });

});

describe("PATCH /api/admin/users/{id}/roles", function () {
    beforeEach(function () {
        $this->adminRole = \App\Models\Role::create(['name' => 'Admin']);
        $this->userRole = \App\Models\Role::create(['name' => 'User']);
        $this->newRole = \App\Models\Role::create(['name' => 'Manager']);

        $this->admin = \App\Models\User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id
        ]);

        $this->user = \App\Models\User::create([
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->userRole->id
        ]);
    });

    afterEach(function () {
        \App\Models\User::query()->delete();
        \App\Models\Role::query()->delete();
    });

    it("Should successfully update user role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => $this->newRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(200);
        expect($response->json('data.message'))->toBe('User role updated successfully.');
        expect($response->json('data.user.role_id'))->toBe($this->newRole->id);

        \Pest\Laravel\assertDatabaseHas('users', [
            'id' => $this->user->id,
            'role_id' => $this->newRole->id
        ]);
    });

    it("Should fail with 404 if user not found", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/9999/roles", [
            'role_id' => $this->newRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(404);
        expect($response->json('errors.message'))->toBe('User not found');
    });

    it("Should fail with validation error if role_id invalid", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => 9999
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.role_id.0'))->toBe('The selected role id is invalid.');
    });

    it("Should return 401 if unauthenticated", function () {
        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => $this->newRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(401);
    });
    it("Should not allow changing role if user is the only admin", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->admin->id}/roles", [
            'role_id' => $this->userRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(422);
        expect($response->json('errors.message'))->toBe('At least one user must have the Admin role.');
    });

    it("Should not update role if role_id is missing", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", []);

        dump($response->json());

        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.role_id.0'))->toBe('The role id field is required.');
    });

    it("Should handle if user already has the same role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => $this->userRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(200);
        expect($response->json('data.message'))->toBe('User role updated successfully.');
    });
    it("Should forbid non-admin user to update roles", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->user);

        $response = $this->patchJson("/api/admin/users/{$this->admin->id}/roles", [
            'role_id' => $this->userRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(403);
    });

    it("Should not allow updating to soft-deleted role", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->newRole->delete();

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => $this->newRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.role_id.0'))->toBe('The selected role id is invalid.');
    });
    it("Should fail if role_id is not an integer", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => 'abc'
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
        expect($response->json('errors.message.role_id.0'))->toBe('The selected role id is invalid.');
    });

    it("Should allow updating another admin if at least one admin remains", function () {
        $secondAdmin = \App\Models\User::create([
            'name' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('rahasia12345'),
            'role_id' => $this->adminRole->id
        ]);

        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$secondAdmin->id}/roles", [
            'role_id' => $this->userRole->id
        ]);

        dump($response->json());

        expect($response)->status()->toBe(200);
        expect($response->json('data.user.role_id'))->toBe($this->userRole->id);
    });
    it("Should fail if role_id is negative", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => -1
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
    });
    it("Should fail if role_id is null", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => null
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
    });
    it("Should fail if role_id contains whitespace string", function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->patchJson("/api/admin/users/{$this->user->id}/roles", [
            'role_id' => '  '
        ]);

        dump($response->json());

        expect($response)->status()->toBe(400);
    });

});

