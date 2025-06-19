<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->renameColumn('purchase_date', 'return_date');
        });
    }

        public function down(): void
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->renameColumn('return_date', 'purchase_date');
        });
    }
};
