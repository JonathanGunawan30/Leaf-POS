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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('user_id')->constrained('users');
            $table->morphs('reference'); // For polymorphic relationships (StockAdjustment, Sale, Purchase, etc.)
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->integer('quantity')->comment('The amount of stock moved (can be positive or negative)');
            $table->string('movement_type')->comment('Type of movement: adjustment, sale, purchase, return, etc.');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
