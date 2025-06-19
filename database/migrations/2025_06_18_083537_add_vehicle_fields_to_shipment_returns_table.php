<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipment_returns', function (Blueprint $table) {
            $table->string('vehicle_number')->nullable()->after('actual_arrival_date');
            $table->enum('vehicle_type', [
                'motorcycle',
                'car_sedan',
                'car_van',
                'car_pickup',
                'truck_small',
                'truck_medium',
                'truck_large',
                'other'
            ])->nullable()->after('vehicle_number');
        });
    }

    public function down(): void
    {
        Schema::table('shipment_returns', function (Blueprint $table) {
            $table->dropColumn(['vehicle_number', 'vehicle_type']);
        });
    }
};
