<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); 

            $table->string('transaction_code', 50)->unique();

            $table->enum('transaction_type', ['deposit', 'withdraw', 'transfer']);

            $table->decimal('amount', 18, 2);

            $table->decimal('balance_before', 18, 2);
            $table->decimal('balance_after', 18, 2);

            $table->unsignedBigInteger('performed_by');
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            $table->dateTime('created_at');

            $table->foreign('performed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
