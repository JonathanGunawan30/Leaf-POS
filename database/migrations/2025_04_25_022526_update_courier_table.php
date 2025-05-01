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
        Schema::table('couriers', function (Blueprint $table) {
            Schema::table('couriers', function (Blueprint $table) {
                $table->dropColumn('vehicle_type');
                $table->dropColumn('vehicle_number');
            });
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->enum('vehicle_type', [
                'motorcycle',
                'car_sedan',
                'car_van',
                'car_pickup',
                'truck_small',
                'truck_medium',
                'truck_large',
                'other'
            ])->after('courier_id');

            $table->string('vehicle_number', 15)->after('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('vehicle_type');
            $table->string('vehicle_number');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
            $table->dropColumn('vehicle_number');
        });
    }
};
