<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'opening_balance')) {
                $table->decimal('opening_balance', 18, 2)->default(0)->after('account_holder_type');
            }
            if (!Schema::hasColumn('customers', 'minimum_balance')) {
                $table->decimal('minimum_balance', 18, 2)->default(0)->after('opening_balance');
            }
            if (!Schema::hasColumn('customers', 'account_opened_at')) {
                $table->dateTime('account_opened_at')->nullable()->after('minimum_balance');
            }
            if (!Schema::hasColumn('customers', 'overdraft_enabled')) {
                $table->boolean('overdraft_enabled')->default(false)->after('monthly_withdrawal_limit');
            }
            if (!Schema::hasColumn('customers', 'authorized_signatory')) {
                $table->string('authorized_signatory', 255)->nullable()->after('overdraft_limit');
            }
            if (!Schema::hasColumn('customers', 'nominee_name')) {
                $table->string('nominee_name', 150)->nullable()->after('authorized_signatory');
            }
            if (!Schema::hasColumn('customers', 'nominee_relation')) {
                $table->string('nominee_relation', 100)->nullable()->after('nominee_name');
            }
            if (!Schema::hasColumn('customers', 'occupation')) {
                $table->string('occupation', 150)->nullable()->after('nominee_relation');
            }
            if (!Schema::hasColumn('customers', 'business_type')) {
                $table->enum('business_type', ['company', 'firm', 'proprietorship', 'other'])->nullable()->after('business_email');
            }
            if (!Schema::hasColumn('customers', 'registration_number')) {
                $table->string('registration_number', 150)->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('customers', 'business_address')) {
                $table->string('business_address', 255)->nullable()->after('registration_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            foreach ([
                'business_address',
                'registration_number',
                'business_type',
                'occupation',
                'nominee_relation',
                'nominee_name',
                'authorized_signatory',
                'overdraft_enabled',
                'account_opened_at',
                'minimum_balance',
                'opening_balance',
            ] as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
