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

        dump($response->json());
        expect($response)->status()->toBe(200);
    });

    it("Should be failed get users current", function () {
        $invalidToken = "invalid-token";

        $response = $this->get("/api/users/current", [
            'Authorization' => "Bearer $invalidToken",
            'Accept' => 'application/json'
        ]);

        dump($response->json());
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

        dump($response->json());
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

        dump($response->json());
        expect($response)->status()->toBe(401);
    });

    it("Should be success update user data even tho request is empty", function () {

        $token = $this->user->createToken('X-API-Token')->plainTextToken;

        $response = $this->patch("/api/users/current", [], [
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        dump($response->json());
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

        dump($response->json());
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

        dump($response->json());
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

        dump($response->json());
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

       dump($response->json());
       expect($response)->status()->toBe(200);
   });

    it("Should be failed logged out current user and return unauthorized", function (){
        $token = "X-WRONG-TOKEN";

        $response = $this->delete("/api/users/logout",[] ,[
            "Authorization" => "Bearer $token",
            "Accept" => "application/json"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(401);
    });
});


