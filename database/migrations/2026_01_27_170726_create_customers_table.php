<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); 

            $table->string('customer_code', 50)->unique();

            $table->string('first_name', 100);
            $table->string('middle_name', 100);
            $table->string('last_name', 100);

            $table->string('fathers_name', 100);
            $table->string('mothers_name', 100);

            $table->date('date_of_birth')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();

            $table->string('permanent_address', 255);
            $table->string('temporary_address', 255);

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->unsignedBigInteger('created_by');

            $table->timestamps(); // created_at & updated_at

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
