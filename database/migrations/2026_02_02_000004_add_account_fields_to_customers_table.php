<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'balance')) {
                $table->decimal('balance', 18, 2)->default(0)->after('account_holder_type');
            }
            if (!Schema::hasColumn('customers', 'minimum_balance')) {
                $table->decimal('minimum_balance', 18, 2)->default(0)->after('balance');
            }
            if (!Schema::hasColumn('customers', 'account_opened_at')) {
                $table->dateTime('account_opened_at')->nullable()->after('minimum_balance');
            }
            if (!Schema::hasColumn('customers', 'occupation')) {
                $table->string('occupation', 150)->nullable()->after('account_opened_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            foreach ([
                'occupation',
                'account_opened_at',
                'minimum_balance',
                'balance',
            ] as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
