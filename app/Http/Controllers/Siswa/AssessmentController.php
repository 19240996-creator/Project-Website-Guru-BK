<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\AssessmentOption;
use App\Models\StudentAssessmentResult;
use App\Models\AuditLog;

class AssessmentController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $assessments = Assessment::where('is_active', true)
            ->with(['studentResults' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->get();

        return view('siswa.asesmen.index', compact('assessments'));
    }

    public function take($id)
    {
        $student = auth()->user()->student;
        $assessment = Assessment::with('questions.options')->where('is_active', true)->findOrFail($id);

        $existingResult = StudentAssessmentResult::where('student_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->first();

        return view('siswa.asesmen.take', compact('assessment', 'existingResult'));
    }

    public function submit(Request $request, $id)
    {
        $student = auth()->user()->student;
        $assessment = Assessment::with('questions.options')->findOrFail($id);

        $answers = $request->input('answers', []);
        $totalScore = 0;

        $dimensions = [
            'R' => ['code' => 'R', 'name' => 'Realistic', 'label' => 'Praktikal & Keteknikan', 'count' => 0, 'score' => 0],
            'I' => ['code' => 'I', 'name' => 'Investigative', 'label' => 'Analitis & Riset Ilmiah', 'count' => 0, 'score' => 0],
            'A' => ['code' => 'A', 'name' => 'Artistic', 'label' => 'Kreatif & Desain Visual', 'count' => 0, 'score' => 0],
            'S' => ['code' => 'S', 'name' => 'Social', 'label' => 'Sosial & Kolaboratif', 'count' => 0, 'score' => 0],
            'E' => ['code' => 'E', 'name' => 'Enterprising', 'label' => 'Kepemimpinan & Wirausaha', 'count' => 0, 'score' => 0],
            'C' => ['code' => 'C', 'name' => 'Conventional', 'label' => 'Terstruktur & Tata Kelola', 'count' => 0, 'score' => 0],
        ];

        $answeredCount = 0;
        foreach ($answers as $questionId => $optionId) {
            $opt = AssessmentOption::find($optionId);
            if ($opt) {
                $code = $opt->dimension_code ? strtoupper($opt->dimension_code) : null;
                if ($code && isset($dimensions[$code])) {
                    $dimensions[$code]['count']++;
                    $dimensions[$code]['score'] += $opt->score_value;
                }
                $totalScore += (int) $opt->score_value;
                $answeredCount++;
            }
        }

        $dimensionScores = [];
        foreach ($dimensions as $code => $data) {
            $percentage = $answeredCount > 0 ? round(($data['count'] / $answeredCount) * 100) : 0;
            $dimensionScores[$code] = [
                'code' => $code,
                'name' => $data['name'],
                'label' => $data['label'],
                'count' => $data['count'],
                'score' => $data['score'],
                'percentage' => $percentage,
            ];
        }

        // Tentukan 2 dimensi teratas (Dominan dan Pendukung)
        $sorted = $dimensions;
        uasort($sorted, function ($a, $b) {
            if ($b['count'] === $a['count']) {
                return $b['score'] <=> $a['score'];
            }
            return $b['count'] <=> $a['count'];
        });

        $keys = array_keys($sorted);
        $primaryCode = $keys[0] ?? 'I';
        $secondaryCode = $keys[1] ?? 'R';

        $profile = $this->getRiasecProfile($primaryCode, $secondaryCode);

        $result = StudentAssessmentResult::updateOrCreate(
            [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'total_score' => $totalScore,
                'dimension_scores' => $dimensionScores,
                'result_category' => $profile['category'],
                'summary' => $profile['summary'],
                'recommendations' => $profile['recommendations'],
                'is_published' => true,
            ]
        );

        AuditLog::log('SELESAI_ASESMEN', 'StudentAssessmentResult', $result->id, "Siswa {$student->name} menyelesaikan asesmen {$assessment->title} dengan tipologi {$profile['category']}.");

        return redirect()->route('siswa.asesmen.result', $result->id)->with('success', 'Asesmen berhasil diselesaikan dan analisis pemetaan minat telah dihitung secara akurat.');
    }

    /**
     * Memetakan kombinasi Holland RIASEC menjadi profil analisis bimbingan konseling yang realistis dan edukatif.
     */
    private function getRiasecProfile(string $p1, string $p2): array
    {
        $code = $p1 . $p2;
        $revCode = $p2 . $p1;

        $knowledgeBase = [
            'IR' => [
                'category' => 'Investigative - Realistic (IR)',
                'summary' => 'Anda memiliki kecenderungan berpikir analitis-kritis yang kuat, dipadukan dengan minat nyata pada eksekusi sistem teknis dan rekayasa komputasi. Anda paling produktif saat membedah logika permasalahan rumit dan mewujudkannya dalam bentuk solusi teknologi atau sistem yang berfungsi optimal.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Teknik Informatika / Ilmu Komputer\n- Rekayasa Perangkat Lunak (Software Engineering)\n- Teknik Komputer / Sistem Komputer\n- Sains Data (Data Science)\n- Teknik Elektro / Mekatronika Terapan\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Software Engineer / Backend Developer\n- Cyber Security & Network Infrastructure Specialist\n- Data Engineer / Data Scientist\n- Embedded Systems & IoT Specialist\n\nSARAN AKSI PENGEMBANGAN DIRI:\nBangun portofolio karya teknis nyata (kode aplikasi, perakitan perangkat, atau proyek sains), asah logika algoritma secara teratur, dan konsultasikan target perguruan tinggi saintek bersama Guru BK.",
            ],
            'IA' => [
                'category' => 'Investigative - Artistic (IA)',
                'summary' => 'Anda memadukan ketajaman riset dan analisis data dengan daya imajinasi kreatif serta kepekaan estetika visual. Anda memiliki bakat merancang produk interaktif yang tidak hanya fungsional secara teknis tetapi juga indah dan ramah bagi pengguna.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Desain Komunikasi Visual (DKV)\n- Desain Produk Digital / UI & UX Design\n- Teknologi Permainan (Game Development)\n- Sistem Informasi Multimedia\n- Arsitektur Digital\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- UI/UX Designer & Product Researcher\n- Frontend Web / Mobile App Developer\n- Game Designer & Creative Technologist\n- Motion Graphic Artist & Digital Animator\n\nSARAN AKSI PENGEMBANGAN DIRI:\nKembangkan portofolio digital di platform kreatif, latih kemahiran software desain standar industri, dan eksplorasi seleksi masuk perguruan tinggi jalur portofolio seni/desain bersama Guru BK.",
            ],
            'IC' => [
                'category' => 'Investigative - Conventional (IC)',
                'summary' => 'Anda memiliki orientasi pemikiran berbasis fakta dan data ilmiah yang ditopang oleh kedisiplinan prosedural, ketelitian tinggi, dan tata kelola dokumentasi terstruktur. Anda unggul dalam audit sistem, pengujian kualitas, dan pemodelan matematis.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Sains Data & Statistika Terapan\n- Sistem Informasi Akuntansi\n- Rekayasa Kualitas Perangkat Lunak (Software QA)\n- Aktuaria & Matematika Komputasi\n- Keamanan Informasi & Manajemen Basis Data\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Data Analyst & Business Intelligence Specialist\n- Quality Assurance (QA) Automation Engineer\n- Database Administrator & Data Governance Specialist\n- Information Systems Auditor\n\nSARAN AKSI PENGEMBANGAN DIRI:\nPerdalam penguasaan pengolahan data terstruktur (SQL, spreadsheet analitis, dan visualisasi data), pelajari metodologi pengujian sistem, dan rencanakan bimbingan studi lanjut bersama Guru BK.",
            ],
            'RC' => [
                'category' => 'Realistic - Conventional (RC)',
                'summary' => 'Anda memiliki keahlian teknis operasional yang andal dengan kepatuhan tinggi terhadap standar operasional prosedur (SOP), kontrol kualitas, dan keandalan sistem fisik. Anda sangat dapat diandalkan dalam pemeliharaan infrastruktur dan pelaksanaan teknis tanpa cela.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Teknik Komputer & Jaringan (TKJ)\n- Teknologi Rekayasa Otomasi & Manufaktur\n- Teknik Telekomunikasi Terapan\n- Manajemen Logistik & Rantai Pasok Industri\n- Teknik Industri Vokasi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Network & Server Infrastructure Administrator\n- Quality Control (QC) & Quality Assurance Specialist\n- Technical Support & Field Operations Engineer\n- Industrial Operations Coordinator\n\nSARAN AKSI PENGEMBANGAN DIRI:\nPersiapkan sertifikasi kompetensi industri (misal: MikroTik MTCNA, Cisco CCNA, atau sertifikasi BNSP), pertahankan kedisiplinan kerja, dan konsultasikan peluang beasiswa ikatan dinas/vokasi dengan Guru BK.",
            ],
            'RA' => [
                'category' => 'Realistic - Artistic (RA)',
                'summary' => 'Anda menggabungkan kecakapan keteknikan dan keterampilan motorik praktis dengan cita rasa estetika visual. Anda gemar mewujudkan konsep artistik menjadi objek atau produk fisik nyata bernilai guna tinggi.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Desain Produk Industri\n- Kriya Seni Terapan & Fabrikasi Digital\n- Arsitektur & Perancangan Interior\n- Teknik Percetakan & Penerbitan Multimedia\n- Rekayasa Audio Visual\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- 3D Modeler & Industrial Prototyper\n- Audio Visual & Lighting Specialist\n- Exhibition & Spatial Designer\n- Pembuat Model Prototipe Produk (Modeler)\n\nSARAN AKSI PENGEMBANGAN DIRI:\nBangun karya fisik atau model 3D mandiri, pelajari piranti lunak perancangan (CAD/Blender), dan diskusikan peluang studi lanjut terapan politeknik seni bersama Guru BK.",
            ],
            'RE' => [
                'category' => 'Realistic - Enterprising (RE)',
                'summary' => 'Anda memiliki keahlian teknis terapan yang dipadukan dengan inisiatif bisnis, kemandirian memimpin proyek, dan dorongan kuat untuk mengubah keahlian menjadi usaha yang bernilai komersial nyata.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Bisnis Digital & E-Commerce\n- Teknik Industri\n- Manajemen Operasional Bisnis\n- Rekayasa Sistem Energi / Infrastruktur\n- Manajemen Bisnis Vokasi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Technical Project Manager\n- Wirausahawan Teknologi / Startup Founder\n- Field Operations Director\n- Technical Sales & Solutions Engineer\n\nSARAN AKSI PENGEMBANGAN DIRI:\nUji coba proyek bisnis rintisan berskala mikro bersama tim, latih kepemimpinan eksekusi teknis, dan ikuti inkubasi kewirausahaan atau program kemitraan industri sekolah bersama Guru BK.",
            ],
            'RS' => [
                'category' => 'Realistic - Social (RS)',
                'summary' => 'Anda senang menggunakan keterampilan teknis dan kerja nyata untuk menolong sesama, mengajari orang lain, atau memfasilitasi kegiatan kemanusiaan di lapangan.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Pendidikan Vokasi / Pendidikan Teknik\n- Keselamatan dan Kesehatan Kerja (K3)\n- Fisioterapi & Ergonomi Terapan\n- Pengembangan Komunitas Teknologi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Instruktur Pelatihan Vokasi & Magang\n- Health, Safety & Environment (HSE) Officer\n- Technical Trainer & Field Educator\n- Community Technology Facilitator\n\nSARAN AKSI PENGEMBANGAN DIRI:\nAsah kemampuan komunikasi instruksional dengan membimbing rekan sebaya, aktif dalam kegiatan kemanusiaan/PMR, dan diskusikan jurusan kependidikan/K3 dengan Guru BK.",
            ],
            'AS' => [
                'category' => 'Artistic - Social (AS)',
                'summary' => 'Anda menggunakan media ekspresi kreatif, karya naratif, dan komunikasi visual untuk menyentuh hati, mengedukasi masyarakat, serta membangun kesadaran sosial yang positif.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Ilmu Komunikasi / Hubungan Masyarakat (PR)\n- Desain Komunikasi Visual (DKV)\n- Psikologi Kreatif & Perkembangan\n- Pendidikan Seni / Bahasa & Sastra\n- Jurnalistik Multimedia\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Creative Content Strategist & Social Campaigner\n- Public Relations Specialist\n- Educational Media Creator\n- Brand Storyteller & Copywriter\n\nSARAN AKSI PENGEMBANGAN DIRI:\nKembangkan portofolio tulisan atau video edukatif, latih kepekaan empati publik, dan diskusikan pilihan prodi rumpun humaniora dan komunikasi bersama Guru BK.",
            ],
            'AE' => [
                'category' => 'Artistic - Enterprising (AE)',
                'summary' => 'Anda memadukan orisinalitas karya kreatif dengan naluri bisnis yang tajam, keberanian berinovasi, dan kecakapan persuasif untuk memasarkan gagasan seni ke pasar komersial.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Manajemen Bisnis Kreatif / Periklanan (Advertising)\n- Pemasaran Digital (Digital Marketing)\n- Desain Manajemen Komunikasi\n- Produksi Film & Televisi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Creative Director & Advertising Lead\n- Digital Marketing Strategist\n- Founder Studio Desain / Production House\n- Content Business Producer\n\nSARAN AKSI PENGEMBANGAN DIRI:\nKerjakan proyek komersial skala kecil untuk klien nyata, pelajari strategi branding dan monetisasi karya, serta rencanakan target studi lanjut industri kreatif dengan Guru BK.",
            ],
            'ES' => [
                'category' => 'Enterprising - Social (ES)',
                'summary' => 'Anda memiliki jiwa kepemimpinan alami yang empatik, piawai berkomunikasi, mengayomi dinamika tim, dan mampu menggerakkan orang banyak menuju visi bersama.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Manajemen Bisnis / Administrasi Bisnis\n- Ilmu Komunikasi & Hubungan Masyarakat\n- Hubungan Internasional\n- Manajemen Sumber Daya Manusia (SDM)\n- Administrasi Publik\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Human Resources (HR) & People Development Lead\n- Business Development Manager\n- Corporate Communications Lead\n- Konsultan Hubungan Publik & Negosiator\n\nSARAN AKSI PENGEMBANGAN DIRI:\nAmbil peran aktif dalam organisasi OSIS/kepemudaan, latih kemampuan bernegosiasi dan presentasi publik, serta petakan kampus tujuan rumpun sosial humaniora bersama Guru BK.",
            ],
            'EC' => [
                'category' => 'Enterprising - Conventional (EC)',
                'summary' => 'Anda unggul dalam kepemimpinan operasional bisnis yang diperkuat oleh kecermatan analisis keuangan, kepatuhan regulasi, serta tata kelola administrasi yang tertata rapi.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Akuntansi Bisnis & Perpajakan\n- Manajemen Keuangan & Perbankan\n- Administrasi Niaga / Logistik Bisnis\n- Hukum Bisnis & Kepatuhan Korporasi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Corporate Banker & Financial Analyst\n- Operations & Procurement Manager\n- Business Administrator & Compliance Officer\n- Analis Kredit & Risiko Perbankan\n\nSARAN AKSI PENGEMBANGAN DIRI:\nPelajari laporan keuangan dasar dan simulasi investasi bisnis, asah ketelitian administrasi, dan koordinasikan rencana studi lanjut rumpun ekonomi/bisnis dengan Guru BK.",
            ],
            'SC' => [
                'category' => 'Social - Conventional (SC)',
                'summary' => 'Anda berdedikasi memberikan pelayanan prima dan pendampingan kepada masyarakat melalui sistem kerja yang tertib, amanah, akurat, dan taat azas prosedural.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Manajemen Informasi Kesehatan (Rekam Medis)\n- Administrasi Pendidikan / Manajemen Lembaga Sekolah\n- Ilmu Perpustakaan & Informasi Terapan\n- Administrasi Publik & Pelayanan Sosial\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Administrator Layanan Akademik / Rumah Sakit\n- Public Service Coordinator\n- Information & Archives Specialist\n- Petugas Pengelola Program Sosial Pemerintah\n\nSARAN AKSI PENGEMBANGAN DIRI:\nLatih pelayanan komunikasi formal yang sopan dan efisien, kuasai sistem arsip digital, dan diskusikan peluang karier di sektor publik/instansi bersama Guru BK.",
            ],
            'SI' => [
                'category' => 'Social - Investigative (SI)',
                'summary' => 'Anda memiliki kepedulian mendalam terhadap perkembangan potensi manusia dan penyelesaian masalah sosial yang dianalisis menggunakan metode ilmiah yang terukur.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Bimbingan dan Konseling (BK)\n- Psikologi Terapan & Perkembangan\n- Kedokteran / Ilmu Keperawatan\n- Sosiologi & Riset Kebijakan Sosial\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Guru BK / Konselor Pendidikan Remaja\n- People Assessment & Training Specialist\n- Peneliti Sosial & Perilaku Konsumen\n- Praktisi Layanan Kesehatan Masyarakat\n\nSARAN AKSI PENGEMBANGAN DIRI:\nAsah keterampilan mendengarkan aktif (*active listening*), ikuti seminar pengembangan diri, dan rencanakan jalur perkuliahan rumpun psikologi/kependidikan bersama Guru BK.",
            ],
            'IE' => [
                'category' => 'Investigative - Enterprising (IE)',
                'summary' => 'Anda menjembatani dunia riset data analitis dengan strategi komersialisasi produk, mampu melihat potensi bisnis dari teknologi baru, dan memimpin inovasi.',
                'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\n- Sistem Informasi Bisnis\n- Manajemen Rekayasa Industri\n- Analitika Bisnis (Business Analytics)\n- Manajemen Teknologi Informasi\n\nPROSPEK KARIER & LAPANGAN INDUSTRI:\n- Product Manager (Teknologi / Digital)\n- Business Analyst & Solutions Architect\n- IT Strategy Consultant\n- Analis Riset Pasar & Venture Capitalist\n\nSARAN AKSI PENGEMBANGAN DIRI:\nPahami hubungan antara metrik bisnis dan arsitektur produk teknologi, pelajari studi kasus startup terkemuka, dan diskusikan rute masa depan bersama Guru BK.",
            ],
        ];

        if (isset($knowledgeBase[$code])) {
            return $knowledgeBase[$code];
        }

        if (isset($knowledgeBase[$revCode])) {
            return $knowledgeBase[$revCode];
        }

        // Fallback dinamis jika kombinasi lainnya
        $dimMap = [
            'R' => ['name' => 'Realistic', 'sub' => 'Praktikal & Keteknikan'],
            'I' => ['name' => 'Investigative', 'sub' => 'Analitis & Riset Ilmiah'],
            'A' => ['name' => 'Artistic', 'sub' => 'Kreatif & Desain Visual'],
            'S' => ['name' => 'Social', 'sub' => 'Sosial & Kolaboratif'],
            'E' => ['name' => 'Enterprising', 'sub' => 'Kepemimpinan & Wirausaha'],
            'C' => ['name' => 'Conventional', 'sub' => 'Terstruktur & Tata Kelola'],
        ];

        $d1 = $dimMap[$p1] ?? $dimMap['I'];
        $d2 = $dimMap[$p2] ?? $dimMap['R'];

        return [
            'category' => "{$d1['name']} - {$d2['name']} ({$p1}{$p2})",
            'summary' => "Orientasi minat Anda menunjukkan perpaduan dominan antara {$d1['name']} ({$d1['sub']}) dan {$d2['name']} ({$d2['sub']}). Anda memiliki dorongan alami untuk menerapkan keahlian di bidang tersebut dalam pemecahan masalah nyata.",
            'recommendations' => "RUMPUN STUDI PERGURUAN TINGGI:\nPilihlah program studi yang mengintegrasikan aspek {$d1['sub']} dan {$d2['sub']}.\n\nPROSPEK KARIER:\nJalur profesi yang memerlukan keahlian terpadu di bidang industri terkait.\n\nSARAN AKSI:\nKonsultasikan target studi dan karier lebih mendalam bersama Guru BK untuk menyelaraskan pilihan rute masa depan.",
        ];
    }

    public function result($id)
    {
        $student = auth()->user()->student;
        $result = StudentAssessmentResult::where('student_id', $student->id)
            ->with('assessment')
            ->findOrFail($id);

        return view('siswa.asesmen.result', compact('result'));
    }
}
