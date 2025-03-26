<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/register', function () {
    return Inertia::render('register');
})->middleware('guest')->name('register');
Route::get('/forgot-password', function () {
    return Inertia::render('forgot_password');
});
Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
});
require __DIR__.'/auth.php';
