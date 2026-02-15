<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'reversal_of')) {
                $table->unsignedBigInteger('reversal_of')->nullable()->after('linked_transaction_id');
                $table->index('reversal_of');
            }
            if (!Schema::hasColumn('transactions', 'is_reversal')) {
                $table->boolean('is_reversal')->default(false)->after('reversal_of');
            }
            if (!Schema::hasColumn('transactions', 'reversal_reason')) {
                $table->text('reversal_reason')->nullable()->after('is_reversal');
            }
            if (!Schema::hasColumn('transactions', 'is_adjustment')) {
                $table->boolean('is_adjustment')->default(false)->after('reversal_reason');
            }
            if (!Schema::hasColumn('transactions', 'adjustment_reason')) {
                $table->text('adjustment_reason')->nullable()->after('is_adjustment');
            }
            if (!Schema::hasColumn('transactions', 'exception_status')) {
                $table->string('exception_status', 50)->nullable()->after('adjustment_reason');
            }
            if (!Schema::hasColumn('transactions', 'exception_reason')) {
                $table->text('exception_reason')->nullable()->after('exception_status');
            }
        });

        try {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'reversal_of')) {
                    $table->foreign('reversal_of')->references('id')->on('transactions')->nullOnDelete();
                }
            });
        } catch (\Throwable $e) {
            // Skip if constraint already exists or cannot be created.
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'exception_reason')) {
                $table->dropColumn('exception_reason');
            }
            if (Schema::hasColumn('transactions', 'exception_status')) {
                $table->dropColumn('exception_status');
            }
            if (Schema::hasColumn('transactions', 'adjustment_reason')) {
                $table->dropColumn('adjustment_reason');
            }
            if (Schema::hasColumn('transactions', 'is_adjustment')) {
                $table->dropColumn('is_adjustment');
            }
            if (Schema::hasColumn('transactions', 'reversal_reason')) {
                $table->dropColumn('reversal_reason');
            }
            if (Schema::hasColumn('transactions', 'is_reversal')) {
                $table->dropColumn('is_reversal');
            }
            if (Schema::hasColumn('transactions', 'reversal_of')) {
                try { $table->dropForeign(['reversal_of']); } catch (\Throwable $e) {}
                $table->dropColumn('reversal_of');
            }
        });
    }
};
