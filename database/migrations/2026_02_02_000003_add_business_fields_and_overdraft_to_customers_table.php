<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'account_holder_type')) {
                $table->enum('account_holder_type', ['individual', 'business'])->default('individual')->after('account_type');
            }
            if (!Schema::hasColumn('customers', 'business_pan_vat')) {
                $table->string('business_pan_vat', 100)->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('customers', 'business_phone')) {
                $table->string('business_phone', 50)->nullable()->after('business_pan_vat');
            }
            if (!Schema::hasColumn('customers', 'business_email')) {
                $table->string('business_email', 150)->nullable()->after('business_phone');
            }
            if (!Schema::hasColumn('customers', 'overdraft_limit')) {
                $table->decimal('overdraft_limit', 12, 2)->nullable()->after('monthly_withdrawal_limit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'overdraft_limit')) {
                $table->dropColumn('overdraft_limit');
            }
            if (Schema::hasColumn('customers', 'business_email')) {
                $table->dropColumn('business_email');
            }
            if (Schema::hasColumn('customers', 'business_phone')) {
                $table->dropColumn('business_phone');
            }
            if (Schema::hasColumn('customers', 'business_pan_vat')) {
                $table->dropColumn('business_pan_vat');
            }
            if (Schema::hasColumn('customers', 'account_holder_type')) {
                $table->dropColumn('account_holder_type');
            }
        });
    }
};
