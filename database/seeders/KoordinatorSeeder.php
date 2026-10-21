<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KoordinatorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Guru Koordinator BK
        User::updateOrCreate(
            ['username' => 'koordinator'],
            [
                'name' => 'Dra. Hj. Siti Rahmawati, M.Pd.',
                'email' => 'koordinator@smk.belajar.id',
                'nip' => '197508151999032001',
                'phone' => '081234567890',
                'role' => 'koordinator_bk',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Akun Guru BK Kedua (untuk pengujian pembagian tugas multi-konselor)
        User::updateOrCreate(
            ['username' => 'gurubk2'],
            [
                'name' => 'Budi Prasetyo, S.Pd., Kons.',
                'email' => 'budi.bk@smk.belajar.id',
                'nip' => '198812102015041002',
                'phone' => '081398765432',
                'role' => 'guru_bk',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Pastikan akun gurubk pertama memiliki NIP jika belum
        $firstGuru = User::where('username', 'gurubk')->first();
        if ($firstGuru && empty($firstGuru->nip)) {
            $firstGuru->update([
                'nip' => '198204122008012015',
                'phone' => $firstGuru->phone ?: '081234567891',
            ]);
        }

        // 3. Berikan penugasan awal siswa ke Guru BK utama agar data eksisting terdistribusi
        $koordinator = User::where('username', 'koordinator')->first();
        if ($firstGuru && $koordinator) {
            $unassignedStudents = \App\Models\Student::whereDoesntHave('counselorAssignment')->get();
            foreach ($unassignedStudents as $st) {
                \App\Models\CounselorAssignment::create([
                    'counselor_id' => $firstGuru->id,
                    'student_id' => $st->id,
                    'student_class_id' => $st->student_class_id,
                    'assigned_by' => $koordinator->id,
                    'notes' => 'Penugasan awal sistem Ruang BK',
                ]);
            }
        }
    }
}
