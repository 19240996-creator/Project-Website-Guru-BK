<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KepalaKampusAdminDicoSeeder extends Seeder
{
    /**
     * Seed akun khusus Kepala Kampus dan Admin DICO sesuai PRD
     */
    public function run(): void
    {
        // 1. Akun Kepala Kampus
        User::updateOrCreate(
            ['username' => 'kepalakampus'],
            [
                'name' => 'Dr. H. Ahmad Fauzi, M.Pd.',
                'email' => 'kepalakampus@smk.belajar.id',
                'nip' => '197203151998021001',
                'phone' => '081234500001',
                'role' => 'kepala_kampus',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Akun Admin DICO
        User::updateOrCreate(
            ['username' => 'admindico'],
            [
                'name' => 'Rendra Wibowo, S.Kom.',
                'email' => 'admindico@smk.belajar.id',
                'nip' => '198905222018031003',
                'phone' => '081234500002',
                'role' => 'admin_dico',
                'password' => Hash::make('password123'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
