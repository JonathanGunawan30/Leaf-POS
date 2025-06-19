<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE shipment_returns
            MODIFY status ENUM(
                'pending',
                'shipped',
                'delivered',
                'cancelled',
                'processing',
                'in transit',
                'on hold',
                'delivered partially'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shipment_returns MODIFY status VARCHAR(255) NOT NULL DEFAULT 'pending'");
    }
};
