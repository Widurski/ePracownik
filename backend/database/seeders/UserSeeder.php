<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'login' => 'admin.systemowy',
            'first_name' => 'Admin',
            'last_name' => 'Systemowy',
            'email' => 'admin@epracownik.pl',
            'phone_number' => '500100200',
            'password' => Hash::make('student123'),
            'role_id' => 3,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jan Kowalski',
            'login' => 'jan.kowalski',
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan.kowalski@epracownik.pl',
            'phone_number' => '501202303',
            'password' => Hash::make('student123'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Anna Nowak',
            'login' => 'anna.nowak',
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'anna.nowak@epracownik.pl',
            'phone_number' => '502303404',
            'password' => Hash::make('student123'),
            'role_id' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Piotr Wiśniewski',
            'login' => 'piotr.wisniewski',
            'first_name' => 'Piotr',
            'last_name' => 'Wiśniewski',
            'email' => 'piotr.wisniewski@epracownik.pl',
            'phone_number' => '503404505',
            'password' => Hash::make('student123'),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }
}
