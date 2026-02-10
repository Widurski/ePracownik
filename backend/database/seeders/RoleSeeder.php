<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nazwa' => 'pracownik', 'opis' => 'Zwykły pracownik firmy', 'created_at' => now(), 'updated_at' => now()],
            ['nazwa' => 'przelozony', 'opis' => 'Przełożony zespołu', 'created_at' => now(), 'updated_at' => now()],
            ['nazwa' => 'administrator', 'opis' => 'Administrator systemu', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
