<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'account_type')) {
                $table->enum('account_type', ['savings', 'current'])->default('savings')->after('account_number');
            }

            if (!Schema::hasColumn('customers', 'business_name')) {
                $table->string('business_name', 255)->nullable()->after('account_type');
            }

            if (!Schema::hasColumn('customers', 'interest_rate')) {
                $table->decimal('interest_rate', 5, 2)->default(5.00)->after('business_name');
            }

            if (!Schema::hasColumn('customers', 'monthly_withdrawal_limit')) {
                $table->unsignedInteger('monthly_withdrawal_limit')->nullable()->after('interest_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'monthly_withdrawal_limit')) {
                $table->dropColumn('monthly_withdrawal_limit');
            }
            if (Schema::hasColumn('customers', 'interest_rate')) {
                $table->dropColumn('interest_rate');
            }
            if (Schema::hasColumn('customers', 'business_name')) {
                $table->dropColumn('business_name');
            }
            if (Schema::hasColumn('customers', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }
};
