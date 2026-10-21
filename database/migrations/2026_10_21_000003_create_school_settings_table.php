<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SMK NEGERI 1 SUMEDANG');
            $table->string('provincial_agency')->default('PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN');
            $table->string('education_branch')->default('CABANG DINAS PENDIDIKAN WILAYAH VIII');
            $table->string('unit_name')->default('UNIT LAYANAN BIMBINGAN, KONSELING & PENGEMBANGAN KARIER SISWA');
            $table->text('address')->nullable();
            $table->string('city', 100)->default('Sumedang');
            $table->string('website')->default('dico-ruangbk.my.id');
            $table->string('phone', 50)->nullable();
            $table->string('postal_code', 20)->nullable()->default('45322');
            $table->string('headmaster_name')->default('Dra. Hj. Elis Herawati, M.Pd.');
            $table->string('headmaster_nip', 50)->default('196804151994032008');
            $table->timestamps();
        });

        // Insert default initial row
        DB::table('school_settings')->insert([
            'id' => 1,
            'school_name' => 'SMK NEGERI 1 SUMEDANG',
            'provincial_agency' => 'PEMERINTAH DAERAH PROVINSI JAWA BARAT — DINAS PENDIDIKAN',
            'education_branch' => 'CABANG DINAS PENDIDIKAN WILAYAH VIII',
            'unit_name' => 'UNIT LAYANAN BIMBINGAN, KONSELING & PENGEMBANGAN KARIER SISWA',
            'address' => 'Jalan Mayor Abdurrachman No. 209, Kotakaler, Kec. Sumedang Utara, Kabupaten Sumedang, Jawa Barat 45322',
            'city' => 'Sumedang',
            'website' => 'dico-ruangbk.my.id',
            'phone' => '(0261) 201531',
            'postal_code' => '45322',
            'headmaster_name' => 'Dra. Hj. Elis Herawati, M.Pd.',
            'headmaster_nip' => '196804151994032008',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
