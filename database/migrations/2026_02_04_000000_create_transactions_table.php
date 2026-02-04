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
            $table->unsignedBigInteger('customer_id');
            $table->enum('transaction_type', ['deposit', 'withdrawal', 'transfer'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->string('reference_number', 50)->unique();
            $table->unsignedBigInteger('linked_transaction_id')->nullable(); // for transfers
            $table->unsignedBigInteger('created_by'); // teller/staff
            $table->unsignedBigInteger('approved_by')->nullable(); // manager
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('restrict');

            $table->foreign('linked_transaction_id')
                  ->references('id')
                  ->on('transactions')
                  ->onDelete('set null');

            $table->index('customer_id');
            $table->index('transaction_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
