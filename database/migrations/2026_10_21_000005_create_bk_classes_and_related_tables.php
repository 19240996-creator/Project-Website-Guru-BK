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
        // 1. Tabel Utama Kelas BK Online
        Schema::create('bk_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('counselor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            
            // Sasaran Peserta
            $table->string('target_grade')->nullable(); // 'X', 'XI', 'XII', atau null untuk Semua
            $table->string('target_major')->nullable(); // misal 'PPLG', 'TKJ', atau null untuk Semua
            $table->json('target_class_ids')->nullable(); // Array ID kelas spesifik [1, 2, 3]

            // Jadwal Pembelajaran & Rentang Absensi
            $table->date('class_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('attendance_start_time');
            $table->time('attendance_end_time');

            // Status Kelas (terjadwal, berlangsung, selesai, dibatalkan)
            $table->string('status')->default('terjadwal');

            $table->timestamps();

            $table->index(['counselor_id', 'class_date']);
        });

        // 2. Tabel Materi Pembelajaran (Slide, Video, Dokumen Tambahan)
        Schema::create('bk_class_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_class_id')->constrained('bk_classes')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('material_type', ['slide', 'video', 'document'])->default('document');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->string('video_url')->nullable(); // YouTube / Vimeo / Drive link
            $table->integer('order_num')->default(0);
            $table->timestamps();

            $table->index(['bk_class_id', 'order_num']);
        });

        // 3. Tabel Tugas Pembelajaran (Assignments)
        Schema::create('bk_class_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_class_id')->constrained('bk_classes')->onDelete('cascade');
            $table->string('title');
            $table->text('instructions');
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('due_time');
            $table->string('allowed_file_types')->default('pdf,docx,doc,ppt,pptx,zip,jpg,jpeg,png');
            $table->integer('max_file_size_kb')->default(20480); // 20 MB
            $table->timestamps();

            $table->index(['bk_class_id', 'due_time']);
        });

        // 4. Tabel Pengumpulan Tugas Siswa
        Schema::create('bk_class_task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_class_task_id')->constrained('bk_class_tasks')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->dateTime('submitted_at');
            $table->enum('status', ['diserahkan', 'terlambat', 'dinilai', 'perlu_revisi'])->default('diserahkan');
            $table->unsignedInteger('score')->nullable(); // 0 - 100
            $table->text('counselor_feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['bk_class_task_id', 'student_id']);
        });

        // 5. Tabel Presensi Kehadiran Kelas BK Siswa
        Schema::create('bk_class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bk_class_id')->constrained('bk_classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->dateTime('attended_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_manual_correction')->default(false);
            $table->text('correction_reason')->nullable();
            $table->foreignId('corrected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Cegah absensi ganda pada kelas yang sama
            $table->unique(['bk_class_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_class_attendances');
        Schema::dropIfExists('bk_class_task_submissions');
        Schema::dropIfExists('bk_class_tasks');
        Schema::dropIfExists('bk_class_materials');
        Schema::dropIfExists('bk_classes');
    }
};
