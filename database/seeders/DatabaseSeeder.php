<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\CounselingCategory;
use App\Models\Counseling;
use App\Models\CounselingFollowUp;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\StudentAssessmentResult;
use App\Models\StudentFuturePlan;
use App\Models\Partner;
use App\Models\PartnerActivity;
use App\Models\Opportunity;
use App\Models\OpportunityRegistration;
use App\Models\AlumniTracking;
use App\Models\Notification;
use App\Models\AuditLog;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic Indonesian school guidance data.
     */
    public function run(): void
    {
        // 1. Users
        $guru = User::create([
            'name' => 'Dra. Endang Sri Rahayu, M.Pd.',
            'username' => 'gurubk',
            'email' => 'guru@sekolah.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'guru_bk',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Academic Years
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // Classes
        $classX = StudentClass::create([
            'academic_year_id' => $academicYear->id,
            'grade' => 'X',
            'major' => 'Teknik Jaringan Komputer dan Telekomunikasi',
            'name' => 'X TJKT 1',
        ]);

        $classXI = StudentClass::create([
            'academic_year_id' => $academicYear->id,
            'grade' => 'XI',
            'major' => 'Teknik Komputer dan Jaringan',
            'name' => 'XI TKJ 1',
        ]);

        $classXII_TKJ = StudentClass::create([
            'academic_year_id' => $academicYear->id,
            'grade' => 'XII',
            'major' => 'Teknik Komputer dan Jaringan',
            'name' => 'XII TKJ 1',
        ]);

        $classXII_RPL = StudentClass::create([
            'academic_year_id' => $academicYear->id,
            'grade' => 'XII',
            'major' => 'Rekayasa Perangkat Lunak',
            'name' => 'XII RPL 1',
        ]);

        $classXII_Mesin = StudentClass::create([
            'academic_year_id' => $academicYear->id,
            'grade' => 'XII',
            'major' => 'Teknik Pemesinan',
            'name' => 'XII TP 1',
        ]);

        // Students & User Accounts
        // Student 1 (Target Kuliah)
        $userSiswa1 = User::create([
            'name' => 'Ahmad Rizky Pratama',
            'username' => '0071234561',
            'email' => 'ahmad.rizky@siswa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '081298765431',
            'is_active' => true,
        ]);

        $siswa1 = Student::create([
            'user_id' => $userSiswa1->id,
            'student_class_id' => $classXII_TKJ->id,
            'nis' => '23241001',
            'nisn' => '0071234561',
            'name' => 'Ahmad Rizky Pratama',
            'gender' => 'L',
            'birth_place' => 'Bandung',
            'birth_date' => '2007-04-12',
            'status' => 'aktif',
            'attention_level' => 'normal',
            'address' => 'Jl. Sukajadi No. 45, Bandung',
            'phone' => '081298765431',
            'parent_name' => 'Bambang Pratama',
            'parent_phone' => '081322334455',
            'parent_job' => 'Karyawan Swasta',
            'special_notes' => 'Siswa memiliki potensi akademik tinggi di bidang jaringan dan matematika.',
        ]);

        StudentAchievement::create([
            'student_id' => $siswa1->id,
            'title' => 'Juara 1 LKS Network Support Tingkat Kota',
            'level' => 'Kota/Kabupaten',
            'year' => 2024,
            'description' => 'Mewakili sekolah pada Lomba Kompetensi Siswa SMK cabang Network System Administration.',
        ]);

        StudentFuturePlan::create([
            'student_id' => $siswa1->id,
            'primary_goal' => 'kuliah',
            'college_target' => 'Institut Teknologi Bandung',
            'study_program' => 'Teknik Informatika',
            'entry_path' => 'SNBP',
            'notes' => 'Memiliki nilai rapor rata-rata 89.5 dan sertifikat LKS.',
            'version' => 1,
        ]);

        // Student 2 (Target Bekerja)
        $userSiswa2 = User::create([
            'name' => 'Siti Nurhaliza',
            'username' => '0071234562',
            'email' => 'siti.nurhaliza@siswa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '082122334455',
            'is_active' => true,
        ]);

        $siswa2 = Student::create([
            'user_id' => $userSiswa2->id,
            'student_class_id' => $classXII_RPL->id,
            'nis' => '23241002',
            'nisn' => '0071234562',
            'name' => 'Siti Nurhaliza',
            'gender' => 'P',
            'birth_place' => 'Cimahi',
            'birth_date' => '2007-08-20',
            'status' => 'aktif',
            'attention_level' => 'perlu_perhatian',
            'address' => 'Jl. Cibabat No. 12, Cimahi',
            'phone' => '082122334455',
            'parent_name' => 'Hasan Basri',
            'parent_phone' => '085711223344',
            'parent_job' => 'Wiraswasta',
            'special_notes' => 'Perlu dorongan dalam penyusunan portofolio kerja dan persiapan wawancara industri.',
        ]);

        StudentFuturePlan::create([
            'student_id' => $siswa2->id,
            'primary_goal' => 'bekerja',
            'work_target_field' => 'Software & Web Development',
            'work_target_company' => 'PT Telekomunikasi Indonesia (Telkom)',
            'notes' => 'Fokus memperdalam frontend development dan Vue/Laravel.',
            'version' => 1,
        ]);

        // Student 3 (Target Wirausaha)
        $userSiswa3 = User::create([
            'name' => 'Budi Santoso',
            'username' => '0071234563',
            'email' => 'budi.santoso@siswa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '083811224466',
            'is_active' => true,
        ]);

        $siswa3 = Student::create([
            'user_id' => $userSiswa3->id,
            'student_class_id' => $classXII_Mesin->id,
            'nis' => '23241003',
            'nisn' => '0071234563',
            'name' => 'Budi Santoso',
            'gender' => 'L',
            'birth_place' => 'Garut',
            'birth_date' => '2006-12-05',
            'status' => 'aktif',
            'attention_level' => 'normal',
            'address' => 'Jl. Kiaracondong No. 89, Bandung',
            'phone' => '083811224466',
            'parent_name' => 'Santoso',
            'parent_phone' => '081988776655',
            'parent_job' => 'Pedagang',
            'special_notes' => 'Aktif dalam praktik bengkel bubut dan CNC.',
        ]);

        StudentFuturePlan::create([
            'student_id' => $siswa3->id,
            'primary_goal' => 'wirausaha',
            'business_field' => 'Fabrikasi Logam dan Bengkel Presisi',
            'business_idea' => 'Mendirikan bengkel modifikasi suku cadang motor presisi dengan mesin bubut lokal.',
            'notes' => 'Mencari program inkubasi wirausaha muda dan bantuan permodalan awal.',
            'version' => 1,
        ]);

        // Student 4 (Belum Menentukan Rencana Masa Depan - Prioritas Guru BK)
        $userSiswa4 = User::create([
            'name' => 'Dewi Lestari',
            'username' => '0071234564',
            'email' => 'dewi.lestari@siswa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '087766554433',
            'is_active' => true,
        ]);

        $siswa4 = Student::create([
            'user_id' => $userSiswa4->id,
            'student_class_id' => $classXII_TKJ->id,
            'nis' => '23241004',
            'nisn' => '0071234564',
            'name' => 'Dewi Lestari',
            'gender' => 'P',
            'birth_place' => 'Sumedang',
            'birth_date' => '2007-02-14',
            'status' => 'aktif',
            'attention_level' => 'prioritas',
            'address' => 'Jl. Cicaheum No. 102, Bandung',
            'phone' => '087766554433',
            'parent_name' => 'Sutrisno',
            'parent_phone' => '081399887766',
            'parent_job' => 'Buruh Harian Lepas',
            'special_notes' => 'Bingung menentukan pilihan karena kendala biaya kuliah namun ingin melanjutkan belajar.',
        ]);

        // Belum punya StudentFuturePlan atau primary_goal = 'belum_menentukan'
        StudentFuturePlan::create([
            'student_id' => $siswa4->id,
            'primary_goal' => 'belum_menentukan',
            'notes' => 'Membutuhkan bimbingan penjajakan beasiswa KIP Kuliah atau program magang berbayar.',
            'version' => 1,
        ]);

        // Student 5 (Alumni Lulusan 2024)
        $userAlumni = User::create([
            'name' => 'Fajar Nugraha, A.Md.T.',
            'username' => '0061234570',
            'email' => 'fajar.nugraha@alumni.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'siswa',
            'phone' => '081233445566',
            'is_active' => true,
        ]);

        $alumniStudent = Student::create([
            'user_id' => $userAlumni->id,
            'student_class_id' => $classXII_TKJ->id,
            'nis' => '22231010',
            'nisn' => '0061234570',
            'name' => 'Fajar Nugraha',
            'gender' => 'L',
            'birth_place' => 'Bandung',
            'birth_date' => '2006-05-18',
            'status' => 'lulus',
            'attention_level' => 'normal',
            'address' => 'Komplek Permata Biru Blok C, Bandung',
            'phone' => '081233445566',
            'parent_name' => 'Deden Nugraha',
            'special_notes' => 'Alumni teladan yang bersedia memberikan sharing session untuk adik kelas.',
        ]);

        AlumniTracking::create([
            'code' => 'ALS-2024-00001',
            'student_id' => $alumniStudent->id,
            'graduation_year' => 2024,
            'tracking_period' => '12_bulan',
            'current_status' => 'bekerja',
            'institution_or_company' => 'PT Telekomunikasi Selular (Telkomsel)',
            'major_or_position' => 'Junior Network Operations Specialist',
            'monthly_income_range' => 'Rp 5.000.000 - Rp 7.500.000',
            'notes' => 'Diterima melalui jalur kerja sama rekrutmen sekolah mitra.',
            'allow_public_showcase' => true,
        ]);

        // 2. Counseling Categories
        $catBelajar = CounselingCategory::create(['name' => 'Masalah Belajar & Akademik', 'description' => 'Kesulitan memahami materi, motivasi belajar, manajemen waktu']);
        $catPribadi = CounselingCategory::create(['name' => 'Pengembangan Pribadi', 'description' => 'Kepercayaan diri, kecemasan, emosi']);
        $catSosial = CounselingCategory::create(['name' => 'Hubungan Sosial & Teman Sebaya', 'description' => 'Interaksi kelas, pergaulan, resolusi konflik']);
        $catKeluarga = CounselingCategory::create(['name' => 'Keluarga & Lingkungan Rumah', 'description' => 'Komunikasi dengan orang tua, kendala keluarga']);
        $catKarier = CounselingCategory::create(['name' => 'Perencanaan Karier & Masa Depan', 'description' => 'Pilihan kuliah, dunia kerja, dan wirausaha']);

        // 3. Counselings
        // Case 1: Diajukan oleh Dewi Lestari (Masuk antrean baru)
        Counseling::create([
            'code' => 'KSL-2026-00001',
            'student_id' => $siswa4->id,
            'category_id' => $catKarier->id,
            'topic' => 'Konsultasi Peluang Beasiswa Kuliah KIP-K',
            'story' => 'Saya ingin sekali kuliah di jurusan sistem informasi, tetapi orang tua merasa ragu dengan pembiayaan kuliah. Saya butuh informasi mengenai beasiswa dan bagaimana meyakinkan orang tua.',
            'urgency' => 'tinggi',
            'preferred_schedule' => 'Kamis siang setelah jam istirahat kedua',
            'status' => 'diajukan',
            'confidential_level' => 'rahasia',
        ]);

        // Case 2: Dijadwalkan hari ini untuk Siti Nurhaliza
        $today = Carbon::today()->toDateString();
        $ksl2 = Counseling::create([
            'code' => 'KSL-2026-00002',
            'student_id' => $siswa2->id,
            'counselor_id' => $guru->id,
            'category_id' => $catKarier->id,
            'topic' => 'Persiapan Portofolio Kerja dan Uji Sertifikasi',
            'story' => 'Saya ingin memastikan kelengkapan portofolio proyek web untuk melamar magang di Telkom.',
            'urgency' => 'sedang',
            'preferred_schedule' => 'Rabu jam 10:00',
            'status' => 'dijadwalkan',
            'scheduled_date' => $today,
            'scheduled_time' => '10:00:00',
            'scheduled_location' => 'Ruang Konseling BK 1',
            'counselor_notes' => 'Catatan internal: Siswa memiliki kemampuan teknis baik, perlu pembinaan pada penyusunan CV ATS-friendly dan teknik wawancara.',
            'confidential_level' => 'rahasia',
            'student_action_plan' => 'Membawa berkas draf portofolio GitHub dan daftar sertifikat kompetensi yang sudah diraih.',
        ]);

        // Case 3: Selesai dengan tindak lanjut jatuh tempo
        $ksl3 = Counseling::create([
            'code' => 'KSL-2026-00003',
            'student_id' => $siswa1->id,
            'counselor_id' => $guru->id,
            'category_id' => $catBelajar->id,
            'topic' => 'Strategi Penguatan Nilai Rapor untuk Jalur SNBP ITB',
            'story' => 'Evaluasi nilai rapor semester 1 sampai 4 untuk pemetaan kuota eligible sekolah.',
            'urgency' => 'sedang',
            'status' => 'tindak_lanjut',
            'scheduled_date' => Carbon::yesterday()->toDateString(),
            'scheduled_time' => '09:00:00',
            'scheduled_location' => 'Ruang Konseling BK 1',
            'counselor_notes' => 'Catatan internal: Tren nilai konsisten naik. Berada di peringkat 3 teratas kuota eligible jurusan.',
            'confidential_level' => 'rahasia',
            'student_action_plan' => 'Melengkapi berkas portofolio piagam kejuaraan LKS dan legalisir rapor semester 1-4.',
        ]);

        // Tindak lanjut jatuh tempo (muncul di "Yang Perlu Dikerjakan")
        CounselingFollowUp::create([
            'counseling_id' => $ksl3->id,
            'action_description' => 'Verifikasi keabsahan sertifikat LKS dan input data portofolio eligible SNBP ke pangkalan data sekolah.',
            'target_date' => $today,
            'status' => 'belum_dilakukan',
            'notes' => 'Koordinasi dengan tim operator kurikulum.',
        ]);

        // 4. Assessments
        $asesmenMinat = Assessment::create([
            'title' => 'Asesmen Minat Karier RIASEC',
            'category' => 'Minat Karier',
            'description' => 'Instrumen penjajakan tipologi minat karier berbasis model John Holland untuk membantu menentukan arah peminatan studi dan profesi.',
            'instructions' => 'Pilihlah salah satu opsi jawaban yang paling menggambarkan kesukaan atau kecenderungan pribadi Anda.',
            'is_active' => true,
        ]);

        $q1 = AssessmentQuestion::create([
            'assessment_id' => $asesmenMinat->id,
            'question_text' => 'Aktivitas mana yang paling Anda nikmati saat memiliki waktu luang?',
            'sort_order' => 1,
        ]);
        AssessmentOption::create(['question_id' => $q1->id, 'option_text' => 'Merakit, membongkar perangkat keras komputer atau mesin fisik', 'score_value' => 5, 'dimension_code' => 'R']);
        AssessmentOption::create(['question_id' => $q1->id, 'option_text' => 'Menganalisis kode program atau memecahkan teka-teki logika', 'score_value' => 5, 'dimension_code' => 'I']);
        AssessmentOption::create(['question_id' => $q1->id, 'option_text' => 'Mendesain tata letak grafis, visual, atau karya multimedia', 'score_value' => 5, 'dimension_code' => 'A']);
        AssessmentOption::create(['question_id' => $q1->id, 'option_text' => 'Mengajarkan sesuatu atau berdiskusi kelompok membantu sesama', 'score_value' => 5, 'dimension_code' => 'S']);

        $q2 = AssessmentQuestion::create([
            'assessment_id' => $asesmenMinat->id,
            'question_text' => 'Ketika menghadapi suatu permasalahan kompleks di tim, peran apa yang Anda ambil?',
            'sort_order' => 2,
        ]);
        AssessmentOption::create(['question_id' => $q2->id, 'option_text' => 'Mencari akar penyebab teknis dengan eksperimen dan data', 'score_value' => 5, 'dimension_code' => 'I']);
        AssessmentOption::create(['question_id' => $q2->id, 'option_text' => 'Memimpin pembagian tugas dan memastikan batas waktu tercapai', 'score_value' => 5, 'dimension_code' => 'E']);
        AssessmentOption::create(['question_id' => $q2->id, 'option_text' => 'Menyusun dokumentasi dan SOP tertata secara teliti', 'score_value' => 5, 'dimension_code' => 'C']);
        AssessmentOption::create(['question_id' => $q2->id, 'option_text' => 'Menjadi penengah dan menjaga keharmonisan anggota tim', 'score_value' => 5, 'dimension_code' => 'S']);

        $q3 = AssessmentQuestion::create([
            'assessment_id' => $asesmenMinat->id,
            'question_text' => 'Lingkungan kerja seperti apa yang membuat Anda bersemangat berprestasi?',
            'sort_order' => 3,
        ]);
        AssessmentOption::create(['question_id' => $q3->id, 'option_text' => 'Laboratorium riset atau pusat komputasi yang terfokus', 'score_value' => 5, 'dimension_code' => 'I']);
        AssessmentOption::create(['question_id' => $q3->id, 'option_text' => 'Ruang kerja kreatif dengan kebebasan mengekspresikan ide', 'score_value' => 5, 'dimension_code' => 'A']);
        AssessmentOption::create(['question_id' => $q3->id, 'option_text' => 'Startup dinamis dengan target bisnis nyata dan negosiasi', 'score_value' => 5, 'dimension_code' => 'E']);
        AssessmentOption::create(['question_id' => $q3->id, 'option_text' => 'Instansi tertib dengan alur kerja yang jelas dan amanah', 'score_value' => 5, 'dimension_code' => 'C']);

        // Assessment Result for Ahmad Rizky
        StudentAssessmentResult::create([
            'student_id' => $siswa1->id,
            'assessment_id' => $asesmenMinat->id,
            'total_score' => 85,
            'result_category' => 'Investigative - Realistic (IR)',
            'summary' => 'Siswa memiliki minat dominan pada pemecahan masalah analitis ilmiah dipadukan dengan kecakapan teknis mekanis/komputasi.',
            'recommendations' => 'Cocok untuk studi lanjut Teknik Informatika, Ilmu Komputer, Sistem Jaringan, atau Rekayasa Komputer.',
            'is_published' => true,
        ]);

        // 5. Partners (Perguruan Tinggi & Perusahaan)
        $itb = Partner::create([
            'code' => 'MIT-2026-00001',
            'type' => 'perguruan_tinggi',
            'category' => 'PTN',
            'name' => 'Institut Teknologi Bandung (ITB)',
            'city' => 'Bandung',
            'address' => 'Jl. Ganesa No. 10, Bandung',
            'website' => 'https://itb.ac.id',
            'contact_person' => 'Dr. Ir. Hendra Prasetyo',
            'phone' => '0222500935',
            'email' => 'humas@itb.ac.id',
            'partnership_doc_number' => 'MOU/ITB-SMK/2024/011',
            'partnership_status' => 'aktif',
            'partnership_start_date' => '2024-01-10',
            'partnership_end_date' => '2027-01-10',
            'notes' => 'Kerja sama sosialisasi jalur masuk perguruan tinggi dan pendampingan olimpiade sains terapan.',
        ]);

        $telkom = Partner::create([
            'code' => 'MIT-2026-00002',
            'type' => 'perusahaan',
            'category' => 'BUMN / Telekomunikasi',
            'name' => 'PT Telekomunikasi Indonesia (Telkom)',
            'city' => 'Bandung',
            'address' => 'Jl. Japati No. 1, Bandung',
            'website' => 'https://telkom.co.id',
            'contact_person' => 'Dian Kusuma, S.Psi. (HR Talent Acquisition)',
            'phone' => '0224521510',
            'email' => 'talent@telkom.co.id',
            'partnership_doc_number' => 'PKS/TELKOM-SMK/2025/089',
            'partnership_status' => 'aktif',
            'partnership_start_date' => '2025-02-01',
            'partnership_end_date' => '2026-11-30',
            'notes' => 'Kerja sama program magang industri bersertifikat dan rekrutmen langsung teknisi muda.',
        ]);

        $astra = Partner::create([
            'code' => 'MIT-2026-00003',
            'type' => 'perusahaan',
            'category' => 'Industri Manufaktur Otomotif',
            'name' => 'PT Astra Honda Motor',
            'city' => 'Jakarta / Karawang',
            'address' => 'Kawasan Industri MM2100, Cikarang Barat',
            'website' => 'https://astra-honda.com',
            'contact_person' => 'Rahmat Hidayat (CSR & Vocational Division)',
            'phone' => '0216518080',
            'email' => 'vocational@astra-honda.com',
            'partnership_doc_number' => 'MOU/AHM-SMK/2024/004',
            'partnership_status' => 'aktif',
            'partnership_start_date' => '2024-06-01',
            'partnership_end_date' => '2026-12-31',
            'notes' => 'Penyelarasan kurikulum vokasi teknik pemesinan dan donasi unit mesin praktik.',
        ]);

        // 6. Partner Activities (Kegiatan Mitra dengan deteksi benturan)
        PartnerActivity::create([
            'code' => 'KGT-2026-00001',
            'partner_id' => $itb->id,
            'title' => 'Sosialisasi Jalur Masuk SNBP, SNBT & KIP Kuliah 2026',
            'activity_type' => 'sosialisasi',
            'date' => Carbon::now()->addDays(3)->toDateString(),
            'start_time' => '08:30:00',
            'end_time' => '11:30:00',
            'room_location' => 'Aula Utama Gedung Serbaguna',
            'target_class_id' => $classXII_TKJ->id,
            'max_participants' => 120,
            'pic_name' => 'Dra. Endang Sri Rahayu, M.Pd.',
            'status' => 'terkonfirmasi',
            'notes' => 'Seluruh siswa kelas XII jurusan IT wajib hadir membawa catatan prestasi.',
        ]);

        PartnerActivity::create([
            'code' => 'KGT-2026-00002',
            'partner_id' => $telkom->id,
            'title' => 'Workshop Industri: Tren Cloud Architecture & Peluang Magang',
            'activity_type' => 'seminar',
            'date' => Carbon::now()->addDays(7)->toDateString(),
            'start_time' => '13:00:00',
            'end_time' => '15:30:00',
            'room_location' => 'Lab Komputer Cisco Lt. 2',
            'target_class_id' => $classXII_RPL->id,
            'max_participants' => 45,
            'pic_name' => 'Koordinator BKK & Tim IT',
            'status' => 'terkonfirmasi',
            'notes' => 'Peserta dibatasi 45 siswa karena kapasitas PC laboratorium.',
        ]);

        // 7. Opportunities (Peluang Siswa)
        $pel1 = Opportunity::create([
            'code' => 'PEL-2026-00001',
            'partner_id' => $telkom->id,
            'title' => 'Program Magang Industri Telkom DigiCamp Batch 5',
            'type' => 'magang',
            'description' => 'Program magang intensif selama 6 bulan untuk siswa SMK kelas XII di unit Digital Service & Network Operations Telkom Group dengan sertifikasi resmi industri.',
            'requirements' => 'Siswa aktif kelas XII Jurusan TKJ atau RPL, nilai rata-rata mata pelajaran produktif minimal 80, melampirkan portofolio proyek mini.',
            'target_audience' => 'Kelas XII TKJ dan RPL',
            'deadline' => Carbon::now()->addDays(14)->toDateString(),
            'quota' => 15,
            'registration_link' => 'https://recruitment.telkom.co.id/digicamp-smk',
            'status' => 'dipublikasikan',
        ]);

        $pel2 = Opportunity::create([
            'code' => 'PEL-2026-00002',
            'partner_id' => $itb->id,
            'title' => 'Beasiswa Prestasi Pendidikan Perintis ITB 2026',
            'type' => 'beasiswa',
            'description' => 'Bimbingan intensif persiapan masuk PTN dan subsidi pembiayaan uang kuliah penuh selama 4 tahun bagi calon mahasiswa berprestasi dari keluarga prasejahtera.',
            'requirements' => 'Siswa kelas XII berpotensi akademik tinggi, memiliki surat keterangan tidak mampu (SKTM) atau pemegang KIP/KPS, komitmen tinggi untuk belajar.',
            'target_audience' => 'Seluruh Siswa Kelas XII',
            'deadline' => Carbon::now()->addDays(20)->toDateString(),
            'quota' => 25,
            'registration_link' => 'https://beasiswa-perintis.itb.ac.id',
            'status' => 'dipublikasikan',
        ]);

        // Registration for Opportunity
        OpportunityRegistration::create([
            'opportunity_id' => $pel1->id,
            'student_id' => $siswa2->id,
            'status' => 'terdaftar',
            'registered_at' => Carbon::now()->subDays(1),
            'notes' => 'Portofolio GitHub dan surat rekomendasi sekolah telah dilampirkan.',
        ]);

        // 8. Notifications
        Notification::create([
            'user_id' => $guru->id,
            'title' => 'Pengajuan Konseling Baru',
            'message' => 'Dewi Lestari (XII TKJ 1) mengajukan konseling kategori Perencanaan Karier.',
            'url' => '/guru/konseling',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $guru->id,
            'title' => 'Tindak Lanjut Konseling Jatuh Tempo',
            'message' => 'Tindak lanjut konseling siswa Ahmad Rizky Pratama jatuh tempo hari ini.',
            'url' => '/guru/konseling',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $userSiswa2->id,
            'title' => 'Jadwal Konseling Telah Dikonfirmasi',
            'message' => 'Jadwal konseling Anda dengan Dra. Endang Sri Rahayu pada hari ini pukul 10:00 di Ruang Konseling BK 1.',
            'url' => '/siswa/konseling',
            'is_read' => false,
        ]);

        // 9. Audit Logs
        AuditLog::create([
            'user_id' => $guru->id,
            'action' => 'PENJADWALAN',
            'entity_type' => 'Counseling',
            'entity_id' => $ksl2->id,
            'description' => 'Guru BK menjadwalkan konseling KSL-2026-00002 untuk siswa Siti Nurhaliza pada hari ini pukul 10:00.',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $guru->id,
            'action' => 'VALIDASI_TINDAK_LANJUT',
            'entity_type' => 'CounselingFollowUp',
            'entity_id' => 1,
            'description' => 'Guru BK membuat rencana tindak lanjut verifikasi sertifikat eligible SNBP untuk Ahmad Rizky Pratama.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
