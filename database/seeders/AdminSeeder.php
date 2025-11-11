<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat akun Admin
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'nip' => '1001',
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'raw_password' => 'password',
                'group' => 'admin',
                'phone' => '081234567890',
                'gender' => 'male',
                'birth_date' => '1990-01-01',
                'birth_place' => 'Jakarta',
                'address' => 'Jl. Admin No. 1',
                'city' => 'Jakarta',
            ]);
        }

        // Membuat akun User
        if (!User::where('email', 'user@example.com')->exists()) {
            User::create([
                'nip' => '2001',
                'name' => 'User Test',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'raw_password' => 'password',
                'group' => 'user',
                'phone' => '081234567891',
                'gender' => 'female',
                'birth_date' => '1995-05-15',
                'birth_place' => 'Bandung',
                'address' => 'Jl. User No. 2',
                'city' => 'Bandung',
            ]);
        }
    }
}
