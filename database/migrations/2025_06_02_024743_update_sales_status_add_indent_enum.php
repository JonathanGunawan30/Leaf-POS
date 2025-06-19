<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'confirmed', 'shipped', 'delivered', 'cancelled', 'indent'
            ])->change();
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 'confirmed', 'shipped', 'delivered', 'cancelled'
            ])->change();
        });
    }
};
