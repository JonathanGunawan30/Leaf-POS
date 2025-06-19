<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);

            $table->dropColumn([
                'product_id',
                'system_stock',
                'actual_stock',
                'difference',
                'last_updated_by',
                'last_updated_at',
            ]);
        });
    }

    public function down()
    {
        Schema::table('stock_opnames', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('system_stock')->default(0);
            $table->integer('actual_stock')->default(0);
            $table->integer('difference')->default(0);
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_updated_at')->nullable();
        });
    }
};
