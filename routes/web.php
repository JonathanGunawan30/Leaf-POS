<?php
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::get('/check-server', function() {
    return $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
});

Route::get('/', function () {
    return Inertia::render('login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('login');

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

Route::get("/login", function () {
    return Inertia::render('login');
});



Route::get('/dashboard', function () {
    return Inertia::render('dashboard');
});

Route::get('/products/all', function () {
    return Inertia::render('all_product');
});

Route::get('/products/create', function () {
    return Inertia::render('product_create');
});

Route::get("/products/print-barcode", function () {
    return Inertia::render('product_barcode');
});

Route::get('/products/restore', function () {
    return Inertia::render('restore_product');
});

Route::get('/products/{id}', function () {
    return Inertia::render('product_details');
});

Route::get('/categories/all', function () {
    return Inertia::render('all_categories');
});

Route::get('/categories/restore', function () {
    return Inertia::render('restore_categories');
});

Route::get('/categories/{id}', function () {
    return Inertia::render('category_details');
});

Route::get("/users/all", function () {
    return Inertia::render('all_users');
});

Route::get("/users/create", function () {
    return Inertia::render('user_create');
});

Route::get("/users/inactive", function () {
    return Inertia::render('activate_users');
});

Route::get("/users/restore", function () {
    return Inertia::render("restore_users");
});

Route::get("/users/{id}", function () {
    return Inertia::render('user_details');
});

Route::get("/taxes/all", function (){
    return Inertia::render("all_taxes");
});

Route::get("/taxes/create", function (){
    return Inertia::render("tax_create");
});

Route::get("/taxes/restore", function (){
    return Inertia::render("restore_taxes");
});

Route::get("/taxes/{id}", function (){
    return Inertia::render("tax_details");
});

Route::get("/units/all", function (){
    return Inertia::render("all_units");
});

Route::get("/units/restore", function (){
    return Inertia::render("restore_units");
});

Route::get("/units/{id}", function (){
    return Inertia::render("unit_details");
});

Route::get("/customers/all", function (){
    return Inertia::render("all_customers");
});

Route::get("/customers/restore", function (){
    return Inertia::render("restore_customers");
});

Route::get('/customers/create', function () {
    return Inertia::render('customer_create');
});

Route::get("/customers/{id}", function (){
    return Inertia::render("customer_details");
});

Route::get("/suppliers/all", function (){
    return Inertia::render("all_suppliers");
});

Route::get("/suppliers/restore", function (){
    return Inertia::render("restore_suppliers");
});

Route::get('/suppliers/create', function () {
    return Inertia::render('supplier_create');
});


Route::get("/suppliers/{id}", function (){
    return Inertia::render("supplier_details");
});

Route::get("/purchases/all", function (){
    return Inertia::render("all_purchases");
});

Route::get("/purchases/restore", function (){
    return Inertia::render("restore_purchases");
});

Route::get('/purchases/create', function () {
    return Inertia::render('purchase_create');
});

Route::get("/purchases/generate-po-issuance", function (){
    return Inertia::render("generate_po_issuance");
});

Route::get("/purchases/{id}", function (){
    return Inertia::render("purchase_details");
});

Route::get("/purchase-returns/all", function (){
    return Inertia::render("purchase_return");
});

Route::get("/purchase-returns/create", function (){
    return Inertia::render("purchase_return_create");
});

Route::get("/purchase-returns/restore", function (){
    return Inertia::render("restore_purchase_returns");
});

Route::get("/purchase-returns/{id}", function (){
    return Inertia::render("purchase_return_details");
});

Route::get("/expense-categories/all", function (){
    return Inertia::render("all_expense_categories");
});

Route::get("/expense-categories/restore", function (){
    return Inertia::render("restore_expense_categories");
});

Route::get("/expense-categories/{id}", function (){
    return Inertia::render("expense_category_details");
});

Route::get("/expenses/all", function (){
    return Inertia::render("all_expenses");
});

Route::get("/expenses/restore", function (){
    return Inertia::render("restore_expenses");
});

Route::get("/expenses/create", function (){
    return Inertia::render("expense_create");
});

Route::get("/expenses/{id}", function (){
    return Inertia::render("expense_details");
});

Route::get("/couriers/all", function (){
    return Inertia::render("all_couriers");
});

Route::get("/couriers/restore", function (){
    return Inertia::render("restore_couriers");
});

Route::get('/couriers/create', function () {
    return Inertia::render('courier_create');
});

Route::get("/couriers/{id}", function (){
    return Inertia::render("courier_details");
});

Route::get("/sales/all", function (){
    return Inertia::render("all_sales");
});

Route::get("/sales/create", function (){
    return Inertia::render("sales_create");
});

Route::get("/sales/restore", function (){
    return Inertia::render("restore_sales");
});

Route::get("/sales/{id}", function (){
    return Inertia::render("sale_details");
});

Route::get("/stock-opname/all", function (){
    return Inertia::render("all_stock_opnames");
});

Route::get("/stock-opname/restore", function (){
    return Inertia::render("restore_stock_opnames");
});

Route::get("/stock-opname/create", function (){
    return Inertia::render("stock_opname_create");
});

Route::get("/stock-adjustment", function (){
    return Inertia::render("stock_adjustment");
});

Route::get("/stock-movement", function (){
    return Inertia::render("stock_movement");
});

Route::get("/stock-opname/{id}", function (){
    return Inertia::render("stock_opname_details");
});


Route::get("/profile/{id}", function (){
    return Inertia::render("Profile/profile");
});

Route::get("/reports", function (){
    return Inertia::render("Reports/reports");
});



Route::fallback(function () {
    return Inertia::render('Errors/NotFound');
});


//require __DIR__.'/auth.php';
