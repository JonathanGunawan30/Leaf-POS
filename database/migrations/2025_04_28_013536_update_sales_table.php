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
        Schema::table('sales', function (Blueprint $table) {
            $table->enum("status", ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])
                ->default("pending")
                ->change();

            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'failed'])
                ->default('unpaid')
                ->change();
        });

        Schema::table("sale_payments", function (Blueprint $table) {
            $table->enum("status", ["unpaid", "paid", "failed"])
                ->default("unpaid")
                ->change();

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'giro'
            ])->after('status')->change();
        });

        Schema::table("purchase_payments", function (Blueprint $table) {
            $table->enum("payment_method", [
                'cash',
                'bank_transfer',
                'giro',
                'credit_card',
                'e_wallet',
                'qris',
                'paypal'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 50)->change();
            $table->string('payment_status', 50)->change();
        });

        Schema::table("sale_payments", function (Blueprint $table) {
            $table->string("status", 50)->change();
            $table->string('payment_method', 50)->change();
        });

        Schema::table("purchase_payments", function (Blueprint $table) {
            $table->string('payment_method', 50)->change();
        });
    }
};
