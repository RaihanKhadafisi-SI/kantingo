<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kantingo.test'],
            [
                'name' => 'Admin Kantin Go',
                'no_hp' => '081234567890',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'penjual@kantingo.test'],
            [
                'name' => 'Bu Rina (Penjual)',
                'no_hp' => '081234567891',
                'role' => 'penjual',
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pembeli@kantingo.test'],
            [
                'name' => 'Raihan (Pembeli)',
                'no_hp' => '081234567892',
                'role' => 'pembeli',
                'password' => 'password',
            ]
        );
    }
}