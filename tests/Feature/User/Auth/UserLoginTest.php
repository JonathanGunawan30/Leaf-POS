<?php

use Illuminate\Support\Facades\Hash;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe("POST /api/users/login", function(){

   it("Should be success login and return user data + token", function (){
       \App\Models\User::create([
           "name" => "Jonathan Gunawan",
           "email" => "jonathan@example.com",
           "password" => Hash::make("rahasia12345"),
           "status" => "active"
       ]);

       $response = $this->post("/api/users/login", [
          "email" => "jonathan@example.com",
          "password" => "rahasia12345"
       ]);

       expect($response)->status()->toBe(200);
   });

    it("Should be failed login because account not active yet", function (){
        \App\Models\User::create([
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => Hash::make("rahasia12345")
        ]);

        $response = $this->post("/api/users/login", [
            "email" => "jonathan@example.com",
            "password" => "rahasia12345"
        ]);

        expect($response)->status()->toBe(403);
    });

    it("Should be failed login and return validation exception", function (){
        \App\Models\User::create([
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => Hash::make("rahasia12345")
        ]);

        $response = $this->post("/api/users/login", [
            "email" => "",
            "password" => ""
        ]);

        expect($response)->status()->toBe(400);
    });

    it("Should be failed login and return invalid credentials", function (){
        \App\Models\User::create([
            "name" => "Jonathan Gunawan",
            "email" => "jonathan@example.com",
            "password" => Hash::make("rahasia12345")
        ]);

        $response = $this->post("/api/users/login", [
            "email" => "invalidcredentials@example.com",
            "password" => "invalidCredentials"
        ]);

        expect($response)->status()->toBe(401);
    });
});
