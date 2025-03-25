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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number", 100)->unique();
            $table->date("purchase_date");
            $table->unsignedBigInteger("total_amount");
            $table->unsignedBigInteger("total_tax")->default(0);
            $table->unsignedBigInteger("total_discount")->default(0);
            $table->unsignedBigInteger("shipping_amount")->default(0);
            $table->unsignedBigInteger("grand_total");
            $table->string("status", 50)->index();
            $table->string("payment_status", 50)->index();
            $table->date("due_date")->nullable();
            $table->date("estimated_arrival_date");
            $table->date("actual_arrival_date")->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("supplier_id")->constrained("suppliers")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
