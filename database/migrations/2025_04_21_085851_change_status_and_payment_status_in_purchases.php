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
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);

            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])
                ->default('pending')
                ->change();
            $table->index('status');

            $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid', 'failed'])
                ->default('unpaid')
                ->change();
            $table->index('payment_status');
        });

        Schema::table("purchase_payments", function (Blueprint $table) {
            $table->dropIndex(['status']);

            $table->enum("status", ["unpaid", "paid", "failed"])
                ->default("unpaid")
                ->change();
            $table->index('status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);

            $table->string('status', 50)->change();
            $table->index('status');

            $table->string('payment_status', 50)->change();
            $table->index('payment_status');
        });

        Schema::table("purchase_payments", function (Blueprint $table) {
            $table->dropIndex(['status']);

            $table->string("status", 50)->change();
            $table->index('status');
        });
    }

};
