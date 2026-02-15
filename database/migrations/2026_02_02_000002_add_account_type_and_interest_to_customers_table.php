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
            if (!Schema::hasColumn('customers', 'interest_rate')) {
                $table->decimal('interest_rate', 5, 2)->default(5.00)->after('account_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'interest_rate')) {
                $table->dropColumn('interest_rate');
            }
            if (Schema::hasColumn('customers', 'account_type')) {
                $table->dropColumn('account_type');
            }
        });
    }
};
