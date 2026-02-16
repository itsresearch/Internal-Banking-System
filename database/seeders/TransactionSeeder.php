<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $customers = Customer::all();

        if ($customers->isEmpty()) {
            return;
        }

        $userId = User::value('id'); 

        for ($i = 0; $i < 100; $i++){

            $customer = $customers->random();
            $amount = $faker->randomFloat(3, 500, 500000);
            $type = $faker->randomElement(['deposit', 'withdrawal']);

            $balanceBefore = $customer->balance ?? 0;

            $balanceAfter = $type === 'deposit'
                ? $balanceBefore + $amount
                : max(0, $balanceBefore - $amount);

            Transaction::create([
                'customer_id' => $customer->id,
                'transaction_type' => $type,
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'status' => 'approved',
                'reference_number' => 'TX-' . strtoupper($faker->bothify('????#####')),
                'created_by' => $userId,
                'approved_by' => $userId,
            ]);
        }
    }
}
