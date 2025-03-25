<?php
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/users", function (){
   it("Should be success register new user and return data", function (){
       $response = $this->post("/api/users", [
           "name" => "Jonathan Gunawan",
           "email" => "jonathan@example.com",
           "password" => "rahasia12345"
       ]);

       dump($response->json());
       expect($response)->status()->toBe(201);

       $this->assertDatabaseHas('users', [
           "name" => "Jonathan Gunawan",
           "email" => "jonathan@example.com"
       ]);

       $user = \App\Models\User::where("email", "jonathan@example.com")->first();

       expect($user)->not()->toBeNull();
   });

    it("Should be failed register new user and throw validation error", function (){
        $response = $this->post("/api/users", [
            "name" => "",
            "email" => "",
            "password" => ""
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
    });

    it("Should be failed register new user and throw error email already exists", function (){
        \App\Models\User::create([
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => "rahasia12345"
        ]);

        $response = $this->post("/api/users", [
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => "rahasia12345"
        ]);

        dump($response->json());
        expect($response)->status()->toBe(400);
    });
});
