<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_documents', function (Blueprint $table) {
            $table->id(); 

            $table->unsignedBigInteger('customer_id');

            $table->enum('document_type', ['citizenship', 'passport', 'photo']);

            $table->string('document_number', 50);

            $table->string('file_path', 255);

            $table->dateTime('uploaded_at');

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_documents');
    }
};
