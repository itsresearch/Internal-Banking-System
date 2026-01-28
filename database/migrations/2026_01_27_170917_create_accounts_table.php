<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); 

            $table->string('account_number', 50)->unique();

            $table->unsignedBigInteger('customer_id');

            $table->enum('account_type', ['savings', 'current']);

            $table->decimal('balance', 18, 2)->default(0.00);

            $table->enum('status', ['active', 'frozen', 'closed'])->default('active');

            $table->unsignedBigInteger('opened_by');

            $table->dateTime('opened_at');

            $table->dateTime('closed_at')->nullable();

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('opened_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
