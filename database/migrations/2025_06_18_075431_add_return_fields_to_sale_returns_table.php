<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->string('invoice_number_returns')->nullable()->after('invoice_number');
            $table->string('delivery_number_returns')->nullable()->after('invoice_number_returns');
            $table->date('invoice_return_issue_date')->nullable()->after('delivery_number_returns');
        });
    }

    public function down(): void
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_number_returns',
                'delivery_number_returns',
                'invoice_return_issue_date',
            ]);
        });
    }
};
