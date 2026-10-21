<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('vice_headmaster_kesiswaan_name')->nullable()->default('Drs. H. Ahmad Sudrajat, M.M.Pd.')->after('headmaster_nip');
            $table->string('vice_headmaster_kesiswaan_nip', 50)->nullable()->default('197203101998021003')->after('vice_headmaster_kesiswaan_name');
            $table->string('vice_headmaster_kurikulum_name')->nullable()->default('Cecep Suhendar, S.T., M.Kom.')->after('vice_headmaster_kesiswaan_nip');
            $table->string('vice_headmaster_kurikulum_nip', 50)->nullable()->default('197805202006041011')->after('vice_headmaster_kurikulum_name');
        });

        // Populate existing row 1 if it exists
        DB::table('school_settings')->where('id', 1)->update([
            'vice_headmaster_kesiswaan_name' => 'Drs. H. Ahmad Sudrajat, M.M.Pd.',
            'vice_headmaster_kesiswaan_nip' => '197203101998021003',
            'vice_headmaster_kurikulum_name' => 'Cecep Suhendar, S.T., M.Kom.',
            'vice_headmaster_kurikulum_nip' => '197805202006041011',
        ]);
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn([
                'vice_headmaster_kesiswaan_name',
                'vice_headmaster_kesiswaan_nip',
                'vice_headmaster_kurikulum_name',
                'vice_headmaster_kurikulum_nip',
            ]);
        });
    }
};
