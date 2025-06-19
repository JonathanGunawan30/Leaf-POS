<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

//Broadcast::channel('stock.alert', function ($user) {
//    if ($user && $user->role && in_array($user->role->name, ['Admin', 'Purchasing', 'Inventory'])) {
//        // Harus return data array minimal user id supaya bisa build auth string
//        return ['id' => $user->id, 'name' => $user->name];
//    }
//    return false;
//});
Broadcast::channel('notifications', function () {
    return true;
});
