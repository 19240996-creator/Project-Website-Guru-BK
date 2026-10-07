<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->enum('role', ['guru_bk', 'siswa'])->default('siswa')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar');
        });

        // Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. 2025/2026
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Student Classes
        Schema::create('student_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->enum('grade', ['X', 'XI', 'XII']);
            $table->string('major'); // e.g. Teknik Komputer & Jaringan, Rekayasa Perangkat Lunak, IPA, IPS
            $table->string('name'); // e.g. XII TKJ 1
            $table->timestamps();
        });

        // Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_class_id')->nullable()->constrained('student_classes')->nullOnDelete();
            $table->string('nis', 30)->unique();
            $table->string('nisn', 30)->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'tidak_aktif'])->default('aktif');
            $table->enum('attention_level', ['normal', 'perlu_perhatian', 'prioritas', 'segera_ditindaklanjuti'])->default('normal');
            $table->text('address')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone', 25)->nullable();
            $table->string('parent_job')->nullable();
            $table->text('special_notes')->nullable(); // internal BK notes regarding family or personal background
            $table->timestamps();
        });

        // Student Achievements
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title');
            $table->string('level'); // Sekolah, Kota/Kabupaten, Provinsi, Nasional, Internasional
            $table->year('year');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Counseling Categories
        Schema::create('counseling_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Counselings Table
        Schema::create('counselings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // KSL-2026-00001
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete(); // Guru BK
            $table->foreignId('category_id')->nullable()->constrained('counseling_categories')->nullOnDelete();
            $table->string('topic');
            $table->text('story')->nullable(); // Siswa's initial problem description
            $table->enum('urgency', ['rendah', 'sedang', 'tinggi', 'mendesak'])->default('sedang');
            $table->string('preferred_schedule')->nullable();
            $table->enum('status', ['diajukan', 'ditinjau', 'dijadwalkan', 'dilaksanakan', 'tindak_lanjut', 'selesai', 'dialihkan'])->default('diajukan');
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->string('scheduled_location')->nullable();
            $table->text('counselor_notes')->nullable(); // STRICTLY CONFIDENTIAL internal counselor notes
            $table->enum('confidential_level', ['umum', 'terbatas', 'rahasia'])->default('rahasia');
            $table->text('student_action_plan')->nullable(); // Kesepakatan / aksi siswa (bisa dilihat siswa)
            $table->timestamps();
        });

        // Counseling Follow Ups
        Schema::create('counseling_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counseling_id')->constrained('counselings')->cascadeOnDelete();
            $table->text('action_description');
            $table->date('target_date');
            $table->enum('status', ['belum_dilakukan', 'sedang_dilakukan', 'selesai', 'ditunda'])->default('belum_dilakukan');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Assessments
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Minat Karier, Gaya Belajar, Kepribadian
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Assessment Questions
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->text('question_text');
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });

        // Assessment Options
        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->integer('score_value')->default(1);
            $table->string('dimension_code', 50)->nullable(); // e.g. R, I, A, S, E, C (RIASEC) or VISUAL, AUDITORY, KINESTETIK
            $table->timestamps();
        });

        // Student Assessment Results
        Schema::create('student_assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->integer('total_score')->default(0);
            $table->string('result_category');
            $table->text('summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Student Future Plans
        Schema::create('student_future_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('primary_goal', ['kuliah', 'bekerja', 'wirausaha', 'kuliah_kerja', 'pelatihan', 'belum_menentukan'])->default('belum_menentukan');
            $table->string('college_target')->nullable();
            $table->string('study_program')->nullable();
            $table->string('entry_path')->nullable(); // SNBP, SNBT, Mandiri, Kedinasan
            $table->string('work_target_field')->nullable();
            $table->string('work_target_company')->nullable();
            $table->string('business_field')->nullable();
            $table->text('business_idea')->nullable();
            $table->text('notes')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        // Partners (Perguruan Tinggi & Perusahaan)
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // MIT-2026-00001
            $table->enum('type', ['perguruan_tinggi', 'perusahaan']);
            $table->string('category')->nullable(); // PTN, PTS, Industri Manufaktur, IT, BUMN, dll
            $table->string('name');
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('email')->nullable();
            $table->string('partnership_doc_number')->nullable();
            $table->enum('partnership_status', ['aktif', 'akan_berakhir', 'berakhir', 'tidak_aktif'])->default('aktif');
            $table->date('partnership_start_date')->nullable();
            $table->date('partnership_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Partner Activities (Kegiatan Mitra dengan Jadwal Anti-bentrok)
        Schema::create('partner_activities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // KGT-2026-00001
            $table->foreignId('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('title');
            $table->enum('activity_type', ['campus_visit', 'seminar', 'sosialisasi', 'kunjungan_industri', 'magang', 'rekrutmen', 'pelatihan']);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_location');
            $table->foreignId('target_class_id')->nullable()->constrained('student_classes')->nullOnDelete();
            $table->integer('max_participants')->default(50);
            $table->string('pic_name')->nullable();
            $table->enum('status', ['rencana', 'menunggu_konfirmasi', 'terkonfirmasi', 'terlaksana', 'ditunda', 'dibatalkan'])->default('terkonfirmasi');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Opportunities (Peluang Siswa)
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // PEL-2026-00001
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['beasiswa', 'magang', 'lowongan_kerja', 'pelatihan', 'sertifikasi', 'kompetisi', 'campus_visit']);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('target_audience')->nullable(); // e.g. Kelas XII, Jurusan TKJ / RPL
            $table->date('deadline')->nullable();
            $table->integer('quota')->nullable();
            $table->string('registration_link')->nullable();
            $table->enum('status', ['draf', 'dipublikasikan', 'ditutup', 'selesai'])->default('dipublikasikan');
            $table->timestamps();
        });

        // Opportunity Registrations
        Schema::create('opportunity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('status', ['terdaftar', 'menunggu', 'diterima', 'ditolak', 'selesai'])->default('terdaftar');
            $table->timestamp('registered_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Alumni Tracking
        Schema::create('alumni_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // ALS-2026-00001
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->year('graduation_year');
            $table->enum('tracking_period', ['3_bulan', '6_bulan', '12_bulan', '24_bulan'])->default('6_bulan');
            $table->enum('current_status', ['bekerja', 'kuliah', 'wirausaha', 'mencari_kerja', 'belum_bekerja', 'belum_terlacak'])->default('belum_terlacak');
            $table->string('institution_or_company')->nullable();
            $table->string('major_or_position')->nullable();
            $table->string('monthly_income_range')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('allow_public_showcase')->default(false); // Jejak Alumni yang boleh tampil ke siswa
            $table->timestamps();
        });

        // Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // SIMPAN, PERBARUI, UBAH_STATUS, HAPUS
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('alumni_trackings');
        Schema::dropIfExists('opportunity_registrations');
        Schema::dropIfExists('opportunities');
        Schema::dropIfExists('partner_activities');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('student_future_plans');
        Schema::dropIfExists('student_assessment_results');
        Schema::dropIfExists('assessment_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('counseling_follow_ups');
        Schema::dropIfExists('counselings');
        Schema::dropIfExists('counseling_categories');
        Schema::dropIfExists('student_achievements');
        Schema::dropIfExists('students');
        Schema::dropIfExists('student_classes');
        Schema::dropIfExists('academic_years');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'phone', 'avatar', 'is_active']);
        });
    }
};
