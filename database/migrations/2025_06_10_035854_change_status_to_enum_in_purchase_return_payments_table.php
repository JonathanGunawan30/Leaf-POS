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
        Schema::table('purchase_return_payments', function (Blueprint $table) {
            $table->enum('status', ['unpaid', 'paid', 'failed'])
                ->default('paid')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_payments', function (Blueprint $table) {
            $table->string('status', 255)->change();
        });
    }
};
