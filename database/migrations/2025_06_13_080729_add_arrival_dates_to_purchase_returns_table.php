<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->date('estimated_arrival_date')->nullable()->after('return_date');
            $table->date('actual_arrival_date')->nullable()->after('estimated_arrival_date');
        });
    }

    public function down()
    {
        Schema::table('purchase_returns', function (Blueprint $table) {
            $table->dropColumn(['estimated_arrival_date', 'actual_arrival_date']);
        });
    }
};
