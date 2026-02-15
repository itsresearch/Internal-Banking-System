<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('customers', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('customers', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('customers', 'is_frozen')) {
                $table->boolean('is_frozen')->default(false)->after('rejected_reason');
            }
            if (!Schema::hasColumn('customers', 'frozen_at')) {
                $table->dateTime('frozen_at')->nullable()->after('is_frozen');
            }
            if (!Schema::hasColumn('customers', 'frozen_reason')) {
                $table->text('frozen_reason')->nullable()->after('frozen_at');
            }
        });

        if (Schema::hasColumn('customers', 'status')) {
            
                DB::statement("ALTER TABLE customers MODIFY COLUMN status ENUM('pending','active','inactive') NOT NULL DEFAULT 'pending'");
            
                Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'approved_by')) {
                    $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'frozen_reason')) {
                $table->dropColumn('frozen_reason');
            }
            if (Schema::hasColumn('customers', 'frozen_at')) {
                $table->dropColumn('frozen_at');
            }
            if (Schema::hasColumn('customers', 'is_frozen')) {
                $table->dropColumn('is_frozen');
            }
            if (Schema::hasColumn('customers', 'rejected_reason')) {
                $table->dropColumn('rejected_reason');
            }
            if (Schema::hasColumn('customers', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('customers', 'approved_by')) {
                try { $table->dropForeign(['approved_by']); } catch (\Throwable $e) {}
                $table->dropColumn('approved_by');
            }
        });
    }
};
