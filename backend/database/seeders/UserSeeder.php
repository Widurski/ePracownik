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
            'imie' => 'Admin',
            'nazwisko' => 'Systemowy',
            'email' => 'admin@epracownik.pl',
            'telefon' => '500100200',
            'password' => Hash::make('admin123'),
            'role_id' => 3,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jan Kowalski',
            'imie' => 'Jan',
            'nazwisko' => 'Kowalski',
            'email' => 'jan.kowalski@epracownik.pl',
            'telefon' => '501202303',
            'password' => Hash::make('pracownik123'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Anna Nowak',
            'imie' => 'Anna',
            'nazwisko' => 'Nowak',
            'email' => 'anna.nowak@epracownik.pl',
            'telefon' => '502303404',
            'password' => Hash::make('pracownik123'),
            'role_id' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Piotr Wiśniewski',
            'imie' => 'Piotr',
            'nazwisko' => 'Wiśniewski',
            'email' => 'piotr.wisniewski@epracownik.pl',
            'telefon' => '503404505',
            'password' => Hash::make('pracownik123'),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }
}
