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
        Schema::dropIfExists('shipment_returns');
        Schema::dropIfExists('sale_return_details');
        Schema::dropIfExists('sale_return_payments');
        Schema::dropIfExists('sale_returns');
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tabel: sale_returns
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 100);
            $table->string('invoice_number_returns', 255)->nullable();
            $table->string('delivery_number_returns', 255)->nullable();
            $table->date('invoice_return_issue_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('compensation_method', ['refund', 'replacement'])->nullable();
            $table->date('return_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'partially paid', 'failed'])->default('unpaid');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('restrict');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
        });

        // Tabel: sale_return_payments
        Schema::create('sale_return_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->string('status', 255)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
        });

        // Tabel: sale_return_details
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
        });

        // Tabel: shipment_returns
        Schema::create('shipment_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('courier_id');
            $table->date('shipping_date')->nullable();
            $table->date('estimated_arrival_date')->nullable();
            $table->date('actual_arrival_date')->nullable();
            $table->string('vehicle_number', 255)->nullable();
            $table->enum('vehicle_type', [
                'motorcycle', 'car_sedan', 'car_van', 'car_pickup',
                'truck_small', 'truck_medium', 'truck_large', 'other'
            ])->nullable();
            $table->enum('status', [
                'pending', 'shipped', 'delivered', 'cancelled',
                'processing', 'in transit', 'on hold', 'delivered partially'
            ])->default('pending');
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('courier_id')->references('id')->on('couriers')->onDelete('restrict');
        });
    }
};
