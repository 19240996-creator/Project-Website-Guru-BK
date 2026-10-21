<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $table = 'school_settings';

    protected $fillable = [
        'school_name',
        'provincial_agency',
        'education_branch',
        'unit_name',
        'address',
        'city',
        'website',
        'phone',
        'postal_code',
        'headmaster_name',
        'headmaster_nip',
        'vice_headmaster_kesiswaan_name',
        'vice_headmaster_kesiswaan_nip',
        'vice_headmaster_kurikulum_name',
        'vice_headmaster_kurikulum_nip',
    ];

    /**
     * Get or create the singleton school setting record.
     */
    public static function get(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
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
                'vice_headmaster_kesiswaan_name' => 'Drs. H. Ahmad Sudrajat, M.M.Pd.',
                'vice_headmaster_kesiswaan_nip' => '197203101998021003',
                'vice_headmaster_kurikulum_name' => 'Cecep Suhendar, S.T., M.Kom.',
                'vice_headmaster_kurikulum_nip' => '197805202006041011',
            ]
        );
    }
}
