<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100)->unique();
            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_discount', 15, 2);
            $table->decimal('shipping_amount', 15, 2);
            $table->decimal('grand_total', 15, 2);
            $table->string('status', 50);
            $table->string('payment_status', 50);
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
