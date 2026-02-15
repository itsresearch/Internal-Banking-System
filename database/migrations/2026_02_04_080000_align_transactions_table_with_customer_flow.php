<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Add columns used by current Teller flow (customer-based)
            if (!Schema::hasColumn('transactions', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('id');
                $table->index('customer_id');
            }

            if (!Schema::hasColumn('transactions', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('customer_id');
                $table->index('created_by');
            }

            if (!Schema::hasColumn('transactions', 'linked_transaction_id')) {
                $table->unsignedBigInteger('linked_transaction_id')->nullable()->after('reference_number');
                $table->index('linked_transaction_id');
            }

            if (!Schema::hasColumn('transactions', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('status');
            }

            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable()->after('rejected_reason');
            }

            if (!Schema::hasColumn('transactions', 'updated_at')) {
                $table->dateTime('updated_at')->nullable()->after('created_at');
                $table->index('updated_at');
            }
        });

        // Backfill created_by from performed_by (old schema)
        if (Schema::hasColumn('transactions', 'performed_by') && Schema::hasColumn('transactions', 'created_by')) {
            DB::statement("
                UPDATE transactions
                SET created_by = performed_by
                WHERE created_by IS NULL
            ");
        }

        // Normalize withdraw -> withdrawal (old enum value)
        if (Schema::hasColumn('transactions', 'transaction_type')) {
            DB::statement("
                UPDATE transactions
                SET transaction_type = 'withdrawal'
                WHERE transaction_type = 'withdraw'
            ");

            // Ensure enum supports 'withdrawal'
            // MySQL/MariaDB: modify enum list
            DB::statement("
                ALTER TABLE transactions
                MODIFY COLUMN transaction_type ENUM('deposit','withdrawal','transfer') NOT NULL
            ");
        }

        // Add foreign keys if not present (best-effort; ignore if already exists)
        try {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'customer_id')) {
                    $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
                }
                if (Schema::hasColumn('transactions', 'created_by')) {
                    $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasColumn('transactions', 'linked_transaction_id')) {
                    $table->foreign('linked_transaction_id')->references('id')->on('transactions')->nullOnDelete();
                }
            });
        } catch (\Throwable $e) {
            // If constraints already exist or DB doesn't allow duplicate names, skip.
        }
    }

    public function down(): void
    {
        // Keep down migration conservative (do not drop existing legacy columns).
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'linked_transaction_id')) {
                try { $table->dropForeign(['linked_transaction_id']); } catch (\Throwable $e) {}
                $table->dropColumn('linked_transaction_id');
            }
            if (Schema::hasColumn('transactions', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('transactions', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
            if (Schema::hasColumn('transactions', 'created_by')) {
                try { $table->dropForeign(['created_by']); } catch (\Throwable $e) {}
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('transactions', 'customer_id')) {
                try { $table->dropForeign(['customer_id']); } catch (\Throwable $e) {}
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('transactions', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};

