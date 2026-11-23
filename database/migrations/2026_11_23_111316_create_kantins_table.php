<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_kantin', 100);
            $table->string('lokasi', 150);
            $table->text('deskripsi')->nullable();
            $table->time('jam_buka')->default('07:00');
            $table->time('jam_tutup')->default('16:00');
            $table->enum('status', ['buka', 'tutup'])->default('buka');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantins');
    }
};