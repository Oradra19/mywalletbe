<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => Hash::make('password123'),
            'balance' => 100000,
        ]);

        User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => Hash::make('password123'),
            'balance' => 100000,
        ]);

        User::create([
            'name' => 'User C',
            'email' => 'userc@example.com',
            'password' => Hash::make('password123'),
            'balance' => 100000,
        ]);
    }
}