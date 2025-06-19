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

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft')->change();
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->string('status', 255)->change();
        });

        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->index('status');
        });
    }


};
