<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Hafifah Yuniarsih', 'email' => 'hafifah@gmail.com'],
            ['name' => 'Ananda Rizkia', 'email' => 'ananda@gmail.com'],
            ['name' => 'Aurellia', 'email' => 'aurellia@gmail.com'],
        ];

        foreach ($customers as $customerData) {
            User::updateOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                    'password' => bcrypt('password123'),
                    'role' => User::ROLE_USER,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
