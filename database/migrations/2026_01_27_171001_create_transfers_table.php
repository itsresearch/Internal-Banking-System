<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('from_customer_id');
            $table->unsignedBigInteger('to_customer_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('from_balance_before', 15, 2);
            $table->decimal('from_balance_after', 15, 2);
            $table->decimal('to_balance_before', 15, 2);
            $table->decimal('to_balance_after', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->string('reference_number', 50)->unique();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('from_customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('to_customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('created_by')
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
        Schema::dropIfExists('transfers');
    }
};
