<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('imie', 50)->after('name');
            $table->string('nazwisko', 50)->after('imie');
            $table->string('telefon', 20)->nullable()->after('nazwisko');
            $table->foreignId('role_id')->default(1)->after('telefon')->constrained('roles');
            $table->boolean('is_active')->default(false)->after('role_id');
            $table->string('activation_token', 64)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['imie', 'nazwisko', 'telefon', 'role_id', 'is_active', 'activation_token']);
        });
    }
};
