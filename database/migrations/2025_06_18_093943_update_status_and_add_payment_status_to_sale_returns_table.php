<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE sale_returns MODIFY payment_status ENUM('unpaid', 'paid', 'partially paid', 'failed') NULL");
        DB::statement("ALTER TABLE sale_returns MODIFY status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sale_returns MODIFY payment_status ENUM('unpaid', 'paid') NULL");
        DB::statement("ALTER TABLE sale_returns MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }
};
