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
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('total_tax', 15, 2)->change();
            $table->decimal('total_discount', 15, 2)->change();
            $table->decimal('shipping_amount', 15, 2)->change();
            $table->decimal('grand_total', 15, 2)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('total_amount')->change();
            $table->unsignedBigInteger('total_tax')->change();
            $table->unsignedBigInteger('total_discount')->change();
            $table->unsignedBigInteger('shipping_amount')->change();
            $table->unsignedBigInteger('grand_total')->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('amount')->change();
        });
    }
};
