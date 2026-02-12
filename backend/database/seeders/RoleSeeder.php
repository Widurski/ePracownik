<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'pracownik', 'description' => 'Zwykły pracownik firmy', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'przelozony', 'description' => 'Przełożony zespołu', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'administrator', 'description' => 'Administrator systemu', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
