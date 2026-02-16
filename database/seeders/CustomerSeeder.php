<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\User;
use Faker\Factory as Faker;


class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('en_NP'); // not working
        for ($i = 0; $i < 100; $i++) { 
        $creatorId = User::inRandomOrder()->value('id') ?? 1;

        $customer = Customer::create([
            'customer_code' => 'CUST-' . strtoupper($faker->unique()->bothify('????##')),
            'account_number' => $faker->unique()->numerify('################'),
            'account_holder_type' => $faker->randomElement(['individual', 'business']),
            'account_type' => $faker->randomElement(['savings', 'current']),
            'interest_rate' => $faker->randomElement([0.00, 5.00, 6.00]),
            'balance' => $faker->randomFloat(2, 1000, 100000),
            'account_opened_at' => $faker->dateTimeBetween('-2 years', 'now'),
            'occupation' => $faker->jobTitle(),
            'first_name' => $faker->firstName(),
            'middle_name' => $faker->optional()->firstName(),
            'last_name' => $faker->lastName(),
            'fathers_name' => $faker->name(),
            'mothers_name' => $faker->name(),
            'date_of_birth' => $faker->date(),
            'gender' => $faker->randomElement(['male', 'female', 'other']),
            'email' => $faker->unique()->safeEmail(),
            'phone' => $faker->phoneNumber(),
            'permanent_address' => $faker->address(),
            'temporary_address' => $faker->address(),
            'status' => $faker->randomElement(['active', 'inactive', 'pending']),
            'approved_at' => $faker->optional()->dateTimeBetween('-1 year', 'now'),
            'created_by' => $creatorId,
        ]);

        $documentNumber = $faker->numerify('######');
        $uploadedAt = $faker->dateTimeBetween('-1 year', 'now');

        $documents = [
            [
                'document_type' => 'photo',
                'document_side' => null,
                'file_path' => 'https://picsum.photos/640/480?random=' . $faker->unique()->randomNumber(5, true),
            ],
            [
                'document_type' => 'citizenship',
                'document_side' => 'front',
                'file_path' => 'https://picsum.photos/640/480?random=' . $faker->unique()->randomNumber(5, true),
            ],
            [
                'document_type' => 'citizenship',
                'document_side' => 'back',
                'file_path' => 'https://picsum.photos/640/480?random=' . $faker->unique()->randomNumber(5, true),
            ],
        ];

        foreach ($documents as $document) {
            CustomerDocument::create([
                'customer_id' => $customer->id,
                'document_type' => $document['document_type'],
                'document_side' => $document['document_side'],
                'document_number' => $documentNumber,
                'file_path' => $document['file_path'],
                'uploaded_at' => $uploadedAt,
            ]);
        }
    }
}
}