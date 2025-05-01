<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::post("/users", [UserController::class, "register"]);
    Route::post("/users/login", [UserController::class, "login"]);
});

Route::middleware('auth:sanctum')->group(function () {
    // ENDPOINT UNTUK SEMUA USER
    Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'get']);
    Route::patch('/users/current', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/logout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::patch('/users/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);


    // ENDPOINT UNTUK ADMIN
    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {

        // USERS MANAGEMENT
        Route::post("/admin/users", [\App\Http\Controllers\AdminController::class, "register"]);
        Route::get("/admin/users/trashed", [\App\Http\Controllers\AdminController::class, "trashed"]);
        Route::get("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "getUserById"]);
        Route::patch("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "updateUserById"]);
        Route::delete("/admin/users/{id}", [\App\Http\Controllers\AdminController::class, "deleteUserById"]);
        Route::patch("/admin/users/{id}/restore", [\App\Http\Controllers\AdminController::class, "restoreUserById"]);
        Route::delete("/admin/users/{id}/force", [\App\Http\Controllers\AdminController::class, "forceDeleteUserById"]);
        Route::get("/admin/users", [\App\Http\Controllers\AdminController::class, "getUsers"]);
        Route::patch("/admin/users/{id}/status", [\App\Http\Controllers\AdminController::class, "updateStatus"]);

        // ROLES MANAGEMENT
        Route::post("/admin/roles", [\App\Http\Controllers\AdminController::class, "addRole"]);
        Route::get("/admin/roles/{id}", [\App\Http\Controllers\AdminController::class, "getRoleById"]);
        Route::patch("/admin/roles/{id}", [\App\Http\Controllers\AdminController::class, "updateRoleById"]);
        Route::delete('/admin/roles/{id}', [\App\Http\Controllers\AdminController::class, "deleteRoleById"]);
        Route::patch("/admin/roles/{id}/restore", [\App\Http\Controllers\AdminController::class, "restoreRoleById"]);
        Route::delete("/admin/roles/{id}/force", [\App\Http\Controllers\AdminController::class, "forceDeleteRoleById"]);
        Route::get("/admin/roles", [\App\Http\Controllers\AdminController::class, "getRoles"]);
        Route::get("/admin/roles/{id}/users", [\App\Http\Controllers\AdminController::class, "getUsersByRoleId"]);
        Route::patch("/admin/users/{id}/roles", [\App\Http\Controllers\AdminController::class, "updateUserRole"]);

        // UNIT MANAGEMENT
        Route::post("/units", [\App\Http\Controllers\UnitController::class, "store"]);
        Route::get("/units/trashed", [\App\Http\Controllers\UnitController::class, "trashed"]);
        Route::get("/units/{id}", [\App\Http\Controllers\UnitController::class, "show"]);
        Route::patch("/units/{id}", [\App\Http\Controllers\UnitController::class, "update"]);
        Route::get("/units", [\App\Http\Controllers\UnitController::class, "index"]);
        Route::delete("/units/{id}", [\App\Http\Controllers\UnitController::class, "delete"]);
        Route::patch("/units/{id}/restore", [\App\Http\Controllers\UnitController::class, "restore"]);
        Route::delete("/units/{id}/force", [\App\Http\Controllers\UnitController::class, "force"]);

    });

    Route::middleware(["role:Admin,Finance"])->group(function () {

        // CATEGORY EXPENSE
        Route::post("/expense-categories", [\App\Http\Controllers\ExpenseCategoryController::class, "createExpenseCategory"]);
        Route::get("/expense-categories/trashed", [\App\Http\Controllers\ExpenseCategoryController::class, "trashed"]);
        Route::patch("/expense-categories/{id}", [\App\Http\Controllers\ExpenseCategoryController::class, "updateExpenseCategory"]);
        Route::get("/expense-categories", [\App\Http\Controllers\ExpenseCategoryController::class, "getExpenseCategory"]);
        Route::delete("/expense-categories/{id}", [\App\Http\Controllers\ExpenseCategoryController::class, "deleteExpenseCategory"]);
        Route::patch("/expense-categories/{id}/restore", [\App\Http\Controllers\ExpenseCategoryController::class, "restoreExpenseCategory"]);
        Route::delete("/expense-categories/{id}/force", [\App\Http\Controllers\ExpenseCategoryController::class, "forceExpenseCategory"]);

        // EXPENSES
        Route::post("/expenses", [\App\Http\Controllers\ExpenseController::class, "store"]);
        Route::get("/expenses/trashed", [\App\Http\Controllers\ExpenseController::class, "trashed"]);
        Route::get("/expenses/{id}", [\App\Http\Controllers\ExpenseController::class, "show"]);
        Route::get("/expenses", [\App\Http\Controllers\ExpenseController::class, "index"]);
        Route::patch("/expenses/{id}", [\App\Http\Controllers\ExpenseController::class, "update"]);
        Route::delete("/expenses/{id}", [\App\Http\Controllers\ExpenseController::class, "delete"]);
        Route::patch("/expenses/{id}/restore", [\App\Http\Controllers\ExpenseController::class, "restore"]);
        Route::delete("/expenses/{id}/force", [\App\Http\Controllers\ExpenseController::class, "force"]);

        // EXPENSE REPORT
        Route::get("/expense-reports", [\App\Http\Controllers\ExpenseController::class, "export"]);

        // TAX MANAGEMENT
        Route::post("/taxes", [\App\Http\Controllers\TaxController::class, "store"]);
        Route::get("/taxes/trashed", [\App\Http\Controllers\TaxController::class, "trashed"]);
        Route::get("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "show"]);
        Route::patch("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "update"]);
        Route::get("/taxes", [\App\Http\Controllers\TaxController::class, "index"]);
        Route::delete("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "delete"]);
        Route::patch("/taxes/{id}/restore", [\App\Http\Controllers\TaxController::class, "restore"]);
        Route::delete("/taxes/{id}/force", [\App\Http\Controllers\TaxController::class, "force"]);

    });

    Route::middleware(["role:Admin,Purchasing"])->group(function () {

        // Category Product
        Route::post("/categories", [\App\Http\Controllers\CategoryController::class, "store"]);
        Route::get("/categories/trashed", [\App\Http\Controllers\CategoryController::class, "trashed"]);
        Route::get("/categories/{id}", [\App\Http\Controllers\CategoryController::class, "show"]);
        Route::patch("/categories/{id}", [\App\Http\Controllers\CategoryController::class, "update"]);
        Route::get("/categories", [\App\Http\Controllers\CategoryController::class, "index"]);
        Route::delete("/categories/{id}", [\App\Http\Controllers\CategoryController::class, "delete"]);
        Route::patch("/categories/{id}/restore", [\App\Http\Controllers\CategoryController::class, "restore"]);
        Route::delete("/categories/{id}/force", [\App\Http\Controllers\CategoryController::class, "force"]);

        // SUPPLIER MANAGEMENT
        Route::post("/suppliers", [\App\Http\Controllers\SupplierController::class, "store"]);
        Route::get("/suppliers/trashed", [\App\Http\Controllers\SupplierController::class, "trashed"]);
        Route::get("/suppliers/{id}", [\App\Http\Controllers\SupplierController::class, "show"]);
        Route::patch("/suppliers/{id}", [\App\Http\Controllers\SupplierController::class, "update"]);
        Route::get("/suppliers", [\App\Http\Controllers\SupplierController::class, "index"]);
        Route::delete("/suppliers/{id}", [\App\Http\Controllers\SupplierController::class, "delete"]);
        Route::patch("/suppliers/{id}/restore", [\App\Http\Controllers\SupplierController::class, "restore"]);
        Route::delete("/suppliers/{id}/force", [\App\Http\Controllers\SupplierController::class, "force"]);

        // PRODUCT MANAGEMENT
        Route::post("/products", [\App\Http\Controllers\ProductController::class, "store"]);
        Route::get("/products/trashed", [\App\Http\Controllers\ProductController::class, "trashed"]);
        Route::get("/products/{id}", [\App\Http\Controllers\ProductController::class, "show"]);
        Route::patch("/products/{id}", [\App\Http\Controllers\ProductController::class, "update"]);
        Route::get("/products", [\App\Http\Controllers\ProductController::class, "index"]);
        Route::delete("/products/{id}", [\App\Http\Controllers\ProductController::class, "delete"]);
        Route::patch("/products/{id}/restore", [\App\Http\Controllers\ProductController::class, "restore"]);
        Route::delete("/products/{id}/force", [\App\Http\Controllers\ProductController::class, "force"]);

        // PURCHASE MANAGEMENT
        Route::post("/purchases", [\App\Http\Controllers\PurchaseController::class, "store"]);
        Route::get("/purchases/trashed", [\App\Http\Controllers\PurchaseController::class, "trashed"]);
        Route::get("/purchases/{id}", [\App\Http\Controllers\PurchaseController::class, "show"]);
        Route::patch("/purchases/{id}", [\App\Http\Controllers\PurchaseController::class, "update"]);
        Route::get("/purchases", [\App\Http\Controllers\PurchaseController::class, "index"]);
        Route::delete("/purchases/{id}", [\App\Http\Controllers\PurchaseController::class, "delete"]);
        Route::patch("/purchases/{id}/restore", [\App\Http\Controllers\PurchaseController::class, "restore"]);
        Route::delete("/purchases/{id}/force", [\App\Http\Controllers\PurchaseController::class, "force"]);

        // PURCHASE PAYMENT MANAGEMENT
        Route::post("/purchase-payments", [\App\Http\Controllers\PurchasePaymentController::class, "store"]);
        Route::get("/purchase-payments/trashed", [\App\Http\Controllers\PurchasePaymentController::class, "trashed"]);
        Route::get("/purchase-payments/{id}", [\App\Http\Controllers\PurchasePaymentController::class, "show"]);
        Route::patch("/purchase-payments/{id}", [\App\Http\Controllers\PurchasePaymentController::class, "update"]);
        Route::get("/purchase-payments", [\App\Http\Controllers\PurchasePaymentController::class, "index"]);
        Route::delete("/purchase-payments/{id}", [\App\Http\Controllers\PurchasePaymentController::class, "delete"]);
        Route::patch("/purchase-payments/{id}/restore", [\App\Http\Controllers\PurchasePaymentController::class, "restore"]);
        Route::delete("/purchase-payments/{id}/force", [\App\Http\Controllers\PurchasePaymentController::class, "force"]);
    });

    Route::middleware(["role:Admin,Sales"])->group(function () {
        // CUSTOMER MANAGEMENT
        Route::post("/customers", [\App\Http\Controllers\CustomerController::class, "store"]);
        Route::get("/customers/trashed", [\App\Http\Controllers\CustomerController::class, "trashed"]);
        Route::get("/customers/{id}", [\App\Http\Controllers\CustomerController::class, "show"]);
        Route::patch("/customers/{id}", [\App\Http\Controllers\CustomerController::class, "update"]);
        Route::get("/customers", [\App\Http\Controllers\CustomerController::class, "index"]);
        Route::delete("/customers/{id}", [\App\Http\Controllers\CustomerController::class, "delete"]);
        Route::patch("/customers/{id}/restore", [\App\Http\Controllers\CustomerController::class, 'restore']);
        Route::delete("/customers/{id}/force", [\App\Http\Controllers\CustomerController::class, 'force']);

        // COURIER MANAGEMENT
        Route::post("/couriers", [\App\Http\Controllers\CourierController::class, "store"]);
        Route::get("/couriers/trashed", [\App\Http\Controllers\CourierController::class, "trashed"]);
        Route::get("/couriers/{id}", [\App\Http\Controllers\CourierController::class, "show"]);
        Route::patch("/couriers/{id}", [\App\Http\Controllers\CourierController::class, "update"]);
        Route::get("/couriers", [\App\Http\Controllers\CourierController::class, "index"]);
        Route::delete("/couriers/{id}", [\App\Http\Controllers\CourierController::class, "delete"]);
        Route::patch("/couriers/{id}/restore", [\App\Http\Controllers\CourierController::class, 'restore']);
        Route::delete("/couriers/{id}/force", [\App\Http\Controllers\CourierController::class, 'force']);


        // SALES MANAGEMENT
        Route::post("/sales", [\App\Http\Controllers\SaleController::class, "store"]);
        Route::get("/sales/{id}", [\App\Http\Controllers\SaleController::class, 'show']);
        Route::patch("/sales/{id}", [\App\Http\Controllers\SaleController::class, 'update']);
    });

});

Route::post('/forgot-password', [UserController::class, 'sendResetLink']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);




