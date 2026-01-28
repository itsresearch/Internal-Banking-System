<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_cash_summary', function (Blueprint $table) {
            $table->id(); 

            $table->date('date');

            $table->decimal('total_cash_in', 18, 2);
            $table->decimal('total_cash_out', 18, 2);

            $table->unsignedBigInteger('generated_by');

            $table->dateTime('created_at');

            $table->foreign('generated_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_cash_summary');
    }
};
