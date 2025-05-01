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
        Schema::table('sales', function (Blueprint $table) {

            $table->string('invoice_number')->nullable()->change();

            $table->string('invoice_downpayment_number')->nullable()->after('invoice_number');

            $table->date("invoice_issue_date")->nullable()->after("invoice_number");
            $table->date('invoice_downpayment_issue_date')->nullable()->after('invoice_downpayment_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('invoice_downpayment_number');
            $table->string('invoice_number')->nullable(false)->change();
            $table->dropColumn('invoice_issue_date');
            $table->dropColumn('invoice_downpayment_issued_at');
        });
    }
};
