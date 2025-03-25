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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('company_name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('phone', 20);
            $table->string('city', 100);
            $table->string('postal_code', 10);
            $table->string('province', 100);
            $table->string('country', 100);
            $table->string('address', 255);
            $table->string('bank_account', 100);
            $table->string('bank_name', 100);
            $table->string('npwp_number', 50)->nullable();
            $table->string('siup_number', 50)->nullable();
            $table->string('nib_number', 50)->nullable();
            $table->string('business_type', 100)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
