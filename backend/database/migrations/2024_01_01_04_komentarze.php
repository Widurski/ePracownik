<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komentarze', function (Blueprint $table) {
            $table->id();
            $table->foreignId('godzina_pracy_id')->constrained('godziny_pracy')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('tresc');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komentarze');
    }
};
