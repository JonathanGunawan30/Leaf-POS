<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketResponseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

Route::middleware("guest")->group(function () {
    Route::post("/users", [UserController::class, "register"]);
    Route::post("/users/login", [UserController::class, "login"]);
});

Route::middleware('auth:api')->get('/user', function () {
    return response()->json(auth()->user());
});

Route::middleware(['auth:api'])->group(function () {
    // ENDPOINT UNTUK SEMUA USER
    Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'get']);
    Route::patch('/users/current', [\App\Http\Controllers\UserController::class, 'update']);
    Route::delete('/users/logout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::patch('/users/change-password', [\App\Http\Controllers\UserController::class, 'changePassword']);
    Route::patch('/users/change-email-or-username', [\App\Http\Controllers\UserController::class, 'changeEmailOrUsername']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('/tickets/{ticket}/responses', [TicketResponseController::class, 'store']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assignAgent']);


    Route::get("/dashboard/summary", [\App\Http\Controllers\DashboardController::class, 'summary']);


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

        Route::patch("/units/{id}", [\App\Http\Controllers\UnitController::class, "update"]);

        Route::delete("/units/{id}", [\App\Http\Controllers\UnitController::class, "delete"]);
        Route::patch("/units/{id}/restore", [\App\Http\Controllers\UnitController::class, "restore"]);
        Route::delete("/units/{id}/force", [\App\Http\Controllers\UnitController::class, "force"]);

    });

    Route::middleware(["role:Admin,Finance"])->group(function () {

        // CATEGORY EXPENSE
        Route::post("/expense-categories", [\App\Http\Controllers\ExpenseCategoryController::class, "createExpenseCategory"]);
        Route::get("/expense-categories/trashed", [\App\Http\Controllers\ExpenseCategoryController::class, "trashed"]);
        Route::get("/expense-categories/{id}", [\App\Http\Controllers\ExpenseCategoryController::class, "getExpenseCategoryById"]);
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

        Route::patch("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "update"]);

        Route::delete("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "delete"]);
        Route::patch("/taxes/{id}/restore", [\App\Http\Controllers\TaxController::class, "restore"]);
        Route::delete("/taxes/{id}/force", [\App\Http\Controllers\TaxController::class, "force"]);

    });

    Route::middleware(["role:Admin,Purchasing"])->group(function () {

        // Category Product
        Route::post("/categories", [\App\Http\Controllers\CategoryController::class, "store"]);
        Route::get("/categories/trashed", [\App\Http\Controllers\CategoryController::class, "trashed"]);

        Route::patch("/categories/{id}", [\App\Http\Controllers\CategoryController::class, "update"]);

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
        Route::patch("/products/{id}", [\App\Http\Controllers\ProductController::class, "update"]);
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

        // PURCHASE RETURN
        Route::post("/purchase-returns", [\App\Http\Controllers\PurchaseReturnController::class, "store"]);
        Route::get("/purchase-returns/trashed", [\App\Http\Controllers\PurchaseReturnController::class, "trashed"]);
        Route::get("/purchase-returns", [\App\Http\Controllers\PurchaseReturnController::class, "index"]);
        Route::get("/purchase-returns/{id}", [\App\Http\Controllers\PurchaseReturnController::class, "show"]);
        Route::patch("/purchase-returns/{id}", [\App\Http\Controllers\PurchaseReturnController::class, "update"]);
        Route::delete("/purchase-returns/{id}", [\App\Http\Controllers\PurchaseReturnController::class, "delete"]);
        Route::patch("/purchase-returns/{id}/restore", [\App\Http\Controllers\PurchaseReturnController::class, "restore"]);
        Route::delete("/purchase-returns/{id}/force", [\App\Http\Controllers\PurchaseReturnController::class, "force"]);

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
        Route::get("/sales/trashed", [\App\Http\Controllers\SaleController::class, "trashed"]);
        Route::get("/sales/{id}", [\App\Http\Controllers\SaleController::class, 'show']);
        Route::patch("/sales/{id}", [\App\Http\Controllers\SaleController::class, 'update']);
        Route::get("/sales", [\App\Http\Controllers\SaleController::class, 'index']);
        Route::delete("/sales/{id}", [\App\Http\Controllers\SaleController::class, 'delete']);
        Route::patch("/sales/{id}/restore", [\App\Http\Controllers\SaleController::class, 'restore']);
        Route::delete("/sales/{id}/force", [\App\Http\Controllers\SaleController::class, 'force']);

        // SURAT JALAN
        Route::get("/sales/{id}/delivery-note", [\App\Http\Controllers\SaleController::class, 'deliveryNote']);

        // SALES REPORT
        Route::get("/sale-reports", [\App\Http\Controllers\SaleController::class, 'export']);

        // SISA PDF EXCEL DAN PAYMENT, SEKALIAN CEK UNTUK SHIPMENT MISALNYA SHIPMENT MAU DIHAPUS GIMANA KALO NANTI RETURN

        Route::get('/sales/shipping-cost/{customer}', [\App\Http\Controllers\SaleController::class, 'getShippingCost']);

        // SALES DETAIL MANAGEMENT
        Route::post('/sales/{id}/details', [\App\Http\Controllers\SaleDetailController::class, 'store']);
        Route::delete('/sales/{saleId}/details/{saleDetailId}', [\App\Http\Controllers\SaleDetailController::class, 'delete']);


        // SALE RETURNS OPTIONAL AJA INI
//        Route::post("/sale-returns", [\App\Http\Controllers\SaleReturnController::class, "store"]);

    });

    // INVENTORY MANAGEMENT
    Route::middleware(["role:Admin,Inventory"])->group(function () {
        // Stock Opname
        Route::post("/stock-opnames", [\App\Http\Controllers\StockOpnameController::class, 'store']);
        Route::get("/stock-opnames/trashed", [\App\Http\Controllers\StockOpnameController::class, 'trashed']);
        Route::get("/stock-opnames", [\App\Http\Controllers\StockOpnameController::class, 'index']);
        Route::get("/stock-opnames/{id}", [\App\Http\Controllers\StockOpnameController::class, 'show']);
        Route::patch("/stock-opnames/{id}", [\App\Http\Controllers\StockOpnameController::class, 'update']);
        Route::delete("/stock-opnames/{id}", [\App\Http\Controllers\StockOpnameController::class, 'destroy']);
        Route::patch("/stock-opnames/{id}/restore", [\App\Http\Controllers\StockOpnameController::class, 'restore']);
        Route::delete("/stock-opnames/{id}/force", [\App\Http\Controllers\StockOpnameController::class, 'forceDelete']);

        // Stock Adjustment
        Route::post("/stock-adjustments", [\App\Http\Controllers\StockAdjustmentController::class, 'store']);
        Route::get("/stock-adjustments", [\App\Http\Controllers\StockAdjustmentController::class, 'index']);
        Route::get("/stock-adjustments/{id}", [\App\Http\Controllers\StockAdjustmentController::class, 'show']);

        // Stock Movement
        Route::get("/stock-movements", [\App\Http\Controllers\StockMovementController::class, 'index']);
        Route::get("/stock-movements/{id}", [\App\Http\Controllers\StockMovementController::class, 'show']);
    });

    Route::middleware(["role:Admin,Purchasing,Finance"])->group(function () {

        // UNIT MANAGEMENT - READ ONLY
        Route::get("/units", [\App\Http\Controllers\UnitController::class, "index"]);
        Route::get("/units/{id}", [\App\Http\Controllers\UnitController::class, "show"]);

        // TAX MANAGEMENT - READ ONLY
        Route::get("/taxes", [\App\Http\Controllers\TaxController::class, "index"]);
        Route::get("/taxes/{id}", [\App\Http\Controllers\TaxController::class, "show"]);
    });


    Route::middleware(["role:Admin,Purchasing,Inventory,Sales"])->group(function () {
        // PRODUCT MANAGEMENT - READ ONLY
        Route::get("/products", [\App\Http\Controllers\ProductController::class, "index"]);
        Route::get("/products/{id}", [\App\Http\Controllers\ProductController::class, "show"]);

        // CATEGORY PRODUCT
        Route::get("/categories", [\App\Http\Controllers\CategoryController::class, "index"]);
        Route::get("/categories/{id}", [\App\Http\Controllers\CategoryController::class, "show"]);
    });

});
Route::middleware(['auth:api'])->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll']);
    Route::post('/notifications', [NotificationController::class, 'store']);
});

Route::post('/notifications/webhook', [NotificationController::class, 'webhook'])->middleware('api');


Route::post('/forgot-password', [UserController::class, 'sendResetLink']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

