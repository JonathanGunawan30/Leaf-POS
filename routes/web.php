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

Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return Inertia::render('register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return Inertia::render('forgot_password');
    });

    Route::get('/reset-password', function () {
        return Inertia::render('reset_password');
    });
});

Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
});
Route::get('/products/all', function () {
    return Inertia::render('all_product');
});


require __DIR__.'/auth.php';
