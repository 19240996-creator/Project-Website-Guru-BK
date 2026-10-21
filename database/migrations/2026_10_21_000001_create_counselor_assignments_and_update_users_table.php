<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom nip dan ubah enum role pada tabel users agar mendukung koordinator_bk
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 50)->nullable()->after('name');
            }
        });

        // Modifikasi tipe kolom role menjadi VARCHAR(50) agar fleksibel mendukung role koordinator_bk, guru_bk, siswa
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'siswa'");
        } catch (\Throwable $e) {
            // Fallback jika database bukan MySQL murni
            try {
                DB::statement("ALTER TABLE users CHANGE COLUMN role role VARCHAR(50) NOT NULL DEFAULT 'siswa'");
            } catch (\Throwable $e2) {
                // Abaikan jika sudah mendukung
            }
        }

        // 2. Tabel penugasan siswa ke Guru BK (counselor_assignments)
        // student_id memiliki indeks UNIQUE sehingga 1 siswa hanya dapat ditugaskan ke 1 Guru BK (mencegah penugasan ganda)
        if (!Schema::hasTable('counselor_assignments')) {
            Schema::create('counselor_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
                $table->foreignId('student_class_id')->constrained('student_classes')->cascadeOnDelete();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['counselor_id', 'student_class_id']);
            });
        }

        // 3. Tabel penugasan kelas ke Guru BK (counselor_class_assignments)
        // student_class_id bersifat UNIQUE sehingga 1 kelas memiliki 1 Guru BK penanggung jawab utama
        if (!Schema::hasTable('counselor_class_assignments')) {
            Schema::create('counselor_class_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('counselor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_class_id')->unique()->constrained('student_classes')->cascadeOnDelete();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('counselor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counselor_assignments');
        Schema::dropIfExists('counselor_class_assignments');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nip')) {
                $table->dropColumn('nip');
            }
        });
    }
};
