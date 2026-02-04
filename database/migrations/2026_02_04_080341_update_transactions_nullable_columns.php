<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` MODIFY `transaction_code` varchar(50) NULL");
            DB::statement("ALTER TABLE `transactions` MODIFY `account_id` bigint unsigned NULL");
            DB::statement("ALTER TABLE `transactions` MODIFY `performed_by` bigint unsigned NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            DB::statement("ALTER TABLE `transactions` MODIFY `transaction_code` varchar(50) NOT NULL");
            DB::statement("ALTER TABLE `transactions` MODIFY `account_id` bigint unsigned NOT NULL");
            DB::statement("ALTER TABLE `transactions` MODIFY `performed_by` bigint unsigned NOT NULL");
        }
    }
};
