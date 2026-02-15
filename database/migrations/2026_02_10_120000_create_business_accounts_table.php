<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            $table->string('business_name', 255)->nullable();
            $table->string('business_pan_vat', 100)->nullable();
            $table->string('business_phone', 50)->nullable();
            $table->string('business_email', 150)->nullable();
            $table->enum('business_type', ['company', 'firm', 'proprietorship', 'other'])->nullable();
            $table->string('registration_number', 150)->nullable();
            $table->string('business_address', 255)->nullable();
            $table->string('authorized_signatory', 255)->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_accounts');
    }
};
