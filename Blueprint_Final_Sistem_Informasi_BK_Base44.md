# BLUEPRINT FINAL — SISTEM INFORMASI BIMBINGAN, PENGEMBANGAN SISWA & KARIER

**Versi:** 1.0  
**Status:** Baseline Development  
**Target Platform:** Base44 / Web Application  
**Bahasa Antarmuka:** Bahasa Indonesia  
**Hak Akses Aplikasi:** 2 role — `Guru BK` dan `Siswa`  
**Prinsip:** Satu data, satu sumber kebenaran, sekali input digunakan untuk banyak proses.

---

# 1. TUJUAN SISTEM

Sistem ini adalah platform terintegrasi untuk membantu Guru BK mengelola seluruh siklus pendampingan siswa, mulai dari data siswa, asesmen, perkembangan, konseling, penanganan masalah, peminatan, perencanaan kuliah/kerja/wirausaha, hubungan dengan perguruan tinggi dan perusahaan mitra, persiapan kelulusan, sampai pelacakan alumni.

Sistem juga menjadi ruang layanan mandiri bagi siswa untuk:

- melihat dan melengkapi profilnya;
- mengikuti asesmen;
- mengajukan konseling;
- melihat jadwal konseling;
- memahami perkembangan dirinya;
- menentukan minat dan rencana masa depan;
- mencari perguruan tinggi, perusahaan, beasiswa, magang, pelatihan dan peluang;
- mengikuti kegiatan sekolah/mitra;
- memperbarui data setelah lulus sebagai alumni.

## Sasaran utama

### Memudahkan Guru BK
- Mengurangi pencatatan berulang.
- Memusatkan data siswa.
- Membantu menentukan siswa yang perlu perhatian.
- Mengelola konseling dari pengajuan sampai tindak lanjut.
- Membuat program dan kegiatan BK.
- Mengelola peminatan dan rencana masa depan siswa.
- Mengelola hubungan dengan perguruan tinggi/perusahaan.
- Membuat laporan otomatis untuk pimpinan sekolah.

### Memudahkan Siswa
- Memiliki profil perkembangan pribadi.
- Dapat meminta konseling tanpa harus selalu datang terlebih dahulu.
- Dapat melihat jadwal dan riwayat layanan yang boleh diakses.
- Dapat melakukan asesmen.
- Dapat menyusun rencana kuliah/kerja/wirausaha.
- Mendapat informasi peluang yang relevan.
- Dapat memperbarui rencana masa depan.

### Memudahkan Sekolah
- Mendapat gambaran agregat kondisi siswa.
- Mendapat laporan BK.
- Mendapat laporan kegiatan dan hubungan mitra.
- Mengetahui rencana lulusan.
- Mengetahui hasil pelacakan lulusan.
- Mendukung pengambilan keputusan berbasis data.

---

# 2. BATASAN HAK AKSES

## Hanya ada 2 role login

### ROLE 1 — GURU BK

Guru BK memiliki akses operasional penuh terhadap data yang memang menjadi kewenangan BK.

Guru BK dapat:
- mengekspor data siswa kelas X, XI dan XII menggunakan import file microsoft excel didalamnya sudah tersimpan data sementara NIS, NISN, Nama Lengkap, No. Handphone, Username dan Password default;
- Data Seperti Kelas dan Jurusan di input atau pilih sebelum import file excel;
- mengelola data siswa;
- mengelola asesmen;
- mengelola layanan BK;
- melihat pengajuan konseling siswa;
- menjadwalkan konseling;
- membuat catatan internal konseling;
- membuat tindak lanjut;
- mengelola program/kegiatan BK;
- mengelola peminatan dan rencana masa depan;
- mengelola perguruan tinggi dan perusahaan;
- mengelola peluang;
- mengelola data kelulusan;
- mengelola alumni dan pelacakan lulusan;
- membuat dan mengekspor laporan;
- membagikan laporan kepada pimpinan melalui file/link yang dibuat sistem.

### ROLE 2 — SISWA

Siswa hanya dapat:
- melihat data dirinya;
- melengkapi data yang diizinkan;
- mengikuti asesmen;
- mengajukan konseling;
- melihat jadwal konseling miliknya;
- melihat informasi layanan yang boleh dilihat;
- melihat perkembangan dirinya;
- mengisi minat;
- membuat rencana masa depan;
- melihat perguruan tinggi/perusahaan/peluang;
- mendaftar kegiatan/peluang;
- memperbarui data alumni setelah lulus;
- menerima notifikasi.

## Tidak ada akun Wakasek atau Kepala Sekolah

Wakasek Kesiswaan, Wakasek Kurikulum dan Kepala Sekolah **tidak menjadi role aplikasi pada versi ini**.

Mereka menerima:
- laporan PDF;
- laporan Excel/CSV bila diperlukan;
- ringkasan dashboard yang diekspor;
- dokumen laporan yang dibuat Guru BK.

Guru BK menentukan jenis laporan, periode dan penerima.

---

# 3. PRINSIP PRIVASI

Sistem wajib menerapkan prinsip:

> **Data yang boleh dilihat siswa != seluruh data yang dimiliki Guru BK.**

## Data rahasia/internal Guru BK

Tidak boleh tampil kepada siswa:
- catatan rahasia konseling;
- catatan interpretasi internal Guru BK;
- kronologi internal kasus;
- penilaian risiko internal;
- catatan tindak lanjut internal;
- catatan rujukan/alih tangan kasus yang bersifat rahasia;
- informasi sensitif lain yang ditandai `RAHASIA`.

## Data yang dapat dilihat siswa

- profil pribadi miliknya;
- hasil asesmen yang ditetapkan dapat dibuka;
- jadwal konseling;
- status pengajuan konseling;
- rencana masa depan;
- perkembangan yang memang dipublikasikan kepada siswa;
- peluang dan kegiatan;
- data pendaftaran dirinya.

## Prinsip penting

Setiap data sensitif memiliki atribut:

`tingkat_kerahasiaan = umum | terbatas | rahasia`

Default untuk catatan konseling adalah `rahasia`.

---

# 4. STRUKTUR MENU GURU BK

## 4.1 Beranda

Widget utama:

- Total siswa
- Siswa perlu perhatian
- Pengajuan konseling baru
- Konseling hari ini
- Konseling minggu ini
- Tindak lanjut jatuh tempo
- Siswa belum memiliki rencana masa depan
- Siswa target kuliah
- Siswa target kerja
- Siswa target wirausaha
- Siswa belum menentukan pilihan
- Peluang aktif
- Kegiatan mendatang
- Perguruan tinggi mitra
- Perusahaan mitra
- Alumni belum terlacak

### Kotak "Yang Perlu Dikerjakan"

Sistem otomatis menampilkan daftar prioritas:
1. Pengajuan konseling belum diproses.
2. Konseling hari ini.
3. Tindak lanjut yang jatuh tempo.
4. Siswa berstatus perlu perhatian.
5. Siswa kelas akhir belum memiliki rencana masa depan.
6. Kegiatan mitra yang akan datang.
7. Data alumni yang perlu diperbarui.

---

# 5. DATA SISWA

## Submenu

- Daftar Siswa
- Profil Siswa
- Data Pribadi
- Data Keluarga
- Data Orang Tua/Wali
- Data Akademik Ringkas
- Kehadiran Ringkas
- Prestasi
- Organisasi/Ekstrakurikuler
- Riwayat Perkembangan

## Profil Siswa 360 Derajat

Setiap siswa memiliki satu halaman terpadu:

### Identitas
- NIS
- NISN
- Nama
- Foto
- Jenis kelamin
- Tempat/tanggal lahir
- Kelas
- Jurusan/program keahlian
- Tahun masuk
- Status siswa

### Keluarga
- Nama orang tua/wali
- Hubungan
- Nomor kontak
- Pekerjaan
- Informasi keluarga yang relevan
- Catatan khusus

### Pendidikan
- Nilai ringkas
- Kehadiran
- Prestasi
- Organisasi
- Ekstrakurikuler

### BK
- Riwayat layanan
- Status pendampingan
- Asesmen
- Peminatan
- Rencana masa depan
- Tindak lanjut aktif

### Karier
- Minat
- Bidang yang diminati
- Pilihan program studi
- Perguruan tinggi tujuan
- Pilihan pekerjaan
- Perusahaan yang diminati

---

# 6. ASESMEN SISWA

## Tujuan

Membantu siswa dan Guru BK mengenali:
- minat;
- bakat;
- kecenderungan bidang;
- gaya belajar;
- pilihan karier;
- kebutuhan pengembangan.

## Struktur asesmen

Setiap asesmen memiliki:

- Nama asesmen
- Deskripsi
- Tujuan
- Instruksi
- Daftar pertanyaan
- Pilihan jawaban
- Skor
- Metode perhitungan
- Kategori hasil
- Interpretasi
- Rekomendasi
- Status aktif/nonaktif

## Alur

`Guru BK membuat asesmen`
→ `Siswa menerima`
→ `Siswa mengerjakan`
→ `Sistem menghitung`
→ `Hasil tersimpan`
→ `Guru BK meninjau`
→ `Guru BK dapat memberikan catatan`
→ `Siswa melihat hasil jika diizinkan`

## Aturan

Hasil asesmen **bukan diagnosis** dan bukan keputusan otomatis terhadap masa depan siswa.

Sistem hanya memberikan bahan pertimbangan.

---

# 7. BIMBINGAN DAN KONSELING

## Submenu Guru BK

- Pengajuan Konseling
- Jadwal Konseling
- Konseling Individu
- Konseling Kelompok
- Bimbingan Klasikal
- Konsultasi
- Kunjungan Rumah
- Alih Tangan Kasus
- Tindak Lanjut

---

# 8. PENGAJUAN KONSELING OLEH SISWA

Siswa membuka:

`Konseling → Ajukan Konseling`

Form:

- Topik
- Kategori
- Cerita singkat
- Tingkat urgensi yang dirasakan siswa
- Pilihan waktu
- Preferensi pertemuan
- Lampiran jika diperlukan

Kategori:
- Belajar
- Pribadi
- Sosial
- Keluarga
- Karier
- Kuliah
- Pekerjaan
- Masa depan
- Lainnya

## Status otomatis

`Diajukan`
→ `Ditinjau BK`
→ `Dijadwalkan`
→ `Dilaksanakan`
→ `Tindak Lanjut`
→ `Selesai`

Alternatif:
`Diajukan → Perlu Informasi Tambahan`
`Diajukan → Dialihkan`

---

# 9. LOGIKA KONSELING

## Ketika siswa mengajukan konseling

Sistem:
1. Membuat nomor permohonan.
2. Menyimpan tanggal dan waktu.
3. Menandai status `DIAJUKAN`.
4. Menampilkan notifikasi kepada Guru BK.
5. Tidak menampilkan catatan internal kepada siswa.

## Guru BK meninjau

Guru BK dapat:
- menerima;
- meminta informasi tambahan;
- menjadwalkan;
- menandai prioritas;
- mengalihkan;
- menutup permohonan dengan alasan.

## Jika dijadwalkan

Sistem membuat:
- tanggal;
- jam;
- tempat/metode;
- Guru BK;
- status jadwal.

Siswa menerima notifikasi.

---

# 10. PENENTUAN PRIORITAS KONSELING

Sistem boleh membantu menentukan prioritas administratif, tetapi tidak boleh mengklaim melakukan diagnosis psikologis.

Contoh tingkat perhatian:

### NORMAL
Tidak ada indikator mendesak.

### PERLU PERHATIAN
Perlu dipantau atau dijadwalkan.

### PRIORITAS
Perlu ditangani lebih cepat.

### SEGERA DITINDAKLANJUTI
Memerlukan perhatian Guru BK sesuai kebijakan sekolah.

Faktor pendukung dapat berasal dari:
- pengajuan siswa;
- kehadiran;
- penurunan akademik;
- laporan observasi;
- riwayat konseling;
- kejadian sekolah;
- asesmen.

Semua faktor harus dapat ditinjau Guru BK.

---

# 11. CATATAN KONSELING

Setelah sesi:

Guru BK dapat menyimpan:

- tanggal;
- jenis layanan;
- topik;
- ringkasan;
- kebutuhan siswa;
- hasil pertemuan;
- kesepakatan;
- rencana tindak lanjut;
- tanggal tindak lanjut;
- tingkat kerahasiaan.

## Jangan menampilkan kepada siswa

- catatan internal;
- interpretasi internal;
- penilaian profesional;
- catatan kasus rahasia.

Siswa hanya melihat:
- tanggal layanan;
- jenis layanan jika diizinkan;
- status;
- jadwal tindak lanjut;
- informasi yang sengaja dibagikan.

---

# 12. TINDAK LANJUT OTOMATIS

Setiap konseling dapat memiliki:

`tindak_lanjut = ya/tidak`

Jika `ya`:
- jenis tindakan;
- tanggal target;
- penanggung jawab;
- status.

Status:
- Belum dilakukan
- Sedang dilakukan
- Selesai
- Ditunda

Jika tanggal target lewat dan belum selesai:

> **MUNCUL DI "YANG PERLU DIKERJAKAN"**

---

# 13. PERKEMBANGAN SISWA

Aspek:

- Pribadi
- Sosial
- Belajar
- Karier
- Kedisiplinan
- Kehadiran
- Prestasi
- Kegiatan

Guru BK dapat membuat catatan perkembangan.

Sistem menyusun kronologi:

`Tanggal → Peristiwa → Tindakan → Hasil → Tindak Lanjut`

---

# 14. PEMINATAN DAN KARIER

## Data yang dikelola

- Minat bidang
- Bakat
- Program studi yang diminati
- Perguruan tinggi yang diminati
- Pekerjaan yang diminati
- Industri yang diminati
- Kompetensi yang dimiliki
- Kompetensi yang perlu dikembangkan
- Cita-cita
- Rencana karier

## Siswa dapat mengubah preferensi

Setiap perubahan disimpan dalam riwayat.

Contoh:

`X: Teknik`
→ `XI: Teknologi`
→ `XII: Informatika`

Guru BK dapat melihat perubahan tersebut.

---

# 15. RENCANA MASA DEPAN

Pilihan utama:

- Kuliah
- Bekerja
- Wirausaha
- Kuliah sambil bekerja
- Pelatihan/sertifikasi
- Belum menentukan
- Pilihan lainnya

## Rencana Kuliah

- Program studi
- Perguruan tinggi
- Jalur masuk
- Target tahun masuk
- Pilihan utama
- Pilihan alternatif
- Pilihan cadangan
- Status pendaftaran
- Hasil seleksi

## Rencana Kerja

- Bidang pekerjaan
- Jabatan/posisi
- Industri
- Perusahaan target
- Lokasi
- Kompetensi yang dibutuhkan
- Status lamaran

## Rencana Wirausaha

- Bidang usaha
- Ide usaha
- Target mulai
- Kebutuhan pengembangan

---

# 16. PERGURUAN TINGGI DAN DUNIA KERJA

## A. Perguruan Tinggi

Data:
- Nama
- Logo
- Jenis
- Lokasi
- Website
- Kontak
- Program studi
- Jalur masuk
- Informasi biaya
- Beasiswa
- Catatan

## B. Perguruan Tinggi Mitra

Tambahan:
- Nomor dokumen kerja sama
- Jenis kerja sama
- Tanggal mulai
- Tanggal berakhir
- Status kerja sama
- Penanggung jawab
- Bentuk kegiatan

Status:
- Aktif
- Akan berakhir
- Berakhir
- Tidak aktif

## C. Perusahaan

Data:
- Nama perusahaan
- Bidang industri
- Lokasi
- Website
- Kontak
- Posisi
- Kompetensi
- Program magang
- Rekrutmen

## D. Perusahaan Mitra

Tambahan:
- Dokumen kerja sama
- Masa berlaku
- Jenis kerja sama
- Penanggung jawab
- Program kerja sama

---

# 17. PENGELOLAAN KERJA SAMA

Sistem harus membedakan:

`DATA MITRA`
dan
`KEGIATAN MITRA`.

Satu perguruan tinggi dapat memiliki banyak kegiatan.

Contoh:

**Universitas A**
- Campus Visit
- Seminar
- Beasiswa
- Sosialisasi PMB

Satu perusahaan dapat memiliki:
- Kunjungan industri
- Magang
- Rekrutmen
- Pelatihan
- Seminar karier

---

# 18. PENJADWALAN RELASI PERGURUAN TINGGI/PERUSAHAAN

Ini menjadi salah satu fungsi utama untuk kebutuhan **Wakasek Kurikulum** melalui laporan Guru BK.

Menu Guru BK:

`Kegiatan Mitra → Jadwal`

Data:
- Mitra
- Jenis kegiatan
- Tanggal
- Jam
- Tempat
- Sasaran kelas
- Jumlah peserta
- Penanggung jawab
- Kebutuhan ruang
- Kebutuhan perangkat
- Status

Status:
- Rencana
- Menunggu Konfirmasi
- Terkonfirmasi
- Terlaksana
- Ditunda
- Dibatalkan

Sistem mencegah benturan jadwal:
- tanggal;
- jam;
- ruang;
- kelas;
- kegiatan.

Jika bentrok:

> **PERINGATAN: Jadwal berbenturan dengan kegiatan lain.**

---

# 19. PELUANG SISWA

Semua peluang masuk ke satu modul:

## Peluang

Jenis:
- Perguruan Tinggi
- Beasiswa
- Magang
- Lowongan Kerja
- Pelatihan
- Sertifikasi
- Kompetisi
- Seminar
- Campus Visit
- Kunjungan Industri
- Kegiatan Mitra

Setiap peluang memiliki:
- Judul
- Mitra
- Jenis
- Deskripsi
- Sasaran
- Persyaratan
- Batas pendaftaran
- Tanggal kegiatan
- Kuota
- Link/informasi
- Status publikasi

---

# 20. LOGIKA REKOMENDASI PELUANG

Sistem dapat mencocokkan:

`kelas + jurusan + minat + rencana masa depan + kompetensi`

Contoh:

Siswa:
- XII TKJ
- Minat teknologi
- Target kerja

Sistem menampilkan lebih dahulu:
- lowongan teknologi;
- magang teknologi;
- sertifikasi teknologi;
- pelatihan teknologi;
- perusahaan mitra yang relevan.

Rekomendasi tidak boleh memblokir peluang lain.

Siswa tetap dapat melihat semua peluang.

---

# 21. KEGIATAN BK

Guru BK dapat membuat:

- Bimbingan klasikal
- Seminar
- Sosialisasi
- Asesmen
- Campus visit
- Job fair
- Parenting
- Pelatihan
- Career day
- Kunjungan industri

Data:
- nama;
- tujuan;
- sasaran;
- tanggal;
- tempat;
- narasumber/mitra;
- peserta;
- materi;
- dokumentasi;
- evaluasi.

---

# 22. PENDAFTARAN KEGIATAN OLEH SISWA

Siswa membuka kegiatan:

`Peluang/Kegiatan → Daftar`

Sistem:
1. memeriksa persyaratan;
2. memeriksa kuota;
3. membuat pendaftaran;
4. mengubah status;
5. mengirim notifikasi.

Status:
- Terdaftar
- Menunggu
- Diterima
- Ditolak
- Hadir
- Tidak hadir
- Selesai

---

# 23. KELULUSAN

Untuk siswa kelas akhir:

Guru BK melihat:

### Peta Rencana Lulusan

- Kuliah
- Kerja
- Wirausaha
- Kuliah sambil kerja
- Pelatihan
- Belum menentukan

Sistem otomatis menampilkan:

> **Siswa kelas XII yang belum memiliki rencana masa depan**

Ini menjadi daftar prioritas pendampingan.

---

# 24. ALUMNI DAN PELACAKAN LULUSAN

Ketika status siswa berubah menjadi `LULUS`:

Sistem membuat/mengubah profil menjadi alumni.

Data:
- tahun lulus;
- jurusan;
- kontak;
- domisili;
- pendidikan;
- pekerjaan;
- usaha.

## Pelacakan

Periode:
- 3 bulan;
- 6 bulan;
- 12 bulan;
- 24 bulan.

Status:
- Bekerja
- Kuliah
- Wirausaha
- Mencari pekerjaan
- Belum bekerja
- Belum terlacak

---

# 25. JEJAK ALUMNI

Jika alumni memberikan persetujuan untuk ditampilkan, data dapat menjadi sumber inspirasi siswa.

Contoh:

> Alumni 2024 — Teknik Komputer  
> Sekarang bekerja sebagai Teknisi di perusahaan X.

Siswa dapat melihat **jejak alumni**, tetapi informasi kontak pribadi hanya ditampilkan jika alumni secara eksplisit mengizinkan.

---

# 26. LAPORAN

Menu laporan hanya ada pada akun Guru BK.

Laporan dibagi menjadi 4 kelompok.

---

## 26.1 LAPORAN UNTUK WAKASEK KESISWAAN

Fokus:

### Kondisi Siswa

- Jumlah siswa
- Distribusi kelas
- Siswa perlu perhatian
- Data kehadiran ringkas
- Prestasi
- Pelanggaran ringkas jika kebijakan sekolah mengizinkan
- Layanan BK
- Kegiatan pembinaan

### Laporan periodik

- Harian
- Mingguan
- Bulanan
- Semester
- Tahunan

### Privasi

Laporan untuk Wakasek Kesiswaan menggunakan **data agregat secara default**.

Detail pribadi hanya dimasukkan jika:
- diperlukan;
- sesuai kewenangan;
- dan diizinkan kebijakan sekolah.

---

# 27. LAPORAN UNTUK WAKASEK KURIKULUM

Fokus pada:

### Penjadwalan dan hubungan akademik/mitra

- Jadwal campus visit
- Seminar perguruan tinggi
- Sosialisasi
- Kunjungan industri
- Pelatihan
- Magang
- Kegiatan perusahaan
- Kegiatan mitra
- Benturan jadwal
- Kelas yang terdampak
- Kebutuhan ruang/jadwal

### Laporan ringkas

`Tanggal | Mitra | Kegiatan | Kelas | Jam | Tempat | Status | Penanggung Jawab`

Tujuannya agar Wakasek Kurikulum dapat mengatur jadwal pembelajaran tanpa perlu masuk ke sistem.

---

# 28. LAPORAN UNTUK KEPALA SEKOLAH

Laporan bersifat eksekutif.

### Dashboard

- Total siswa
- Layanan BK
- Siswa perlu perhatian
- Rencana masa depan siswa
- Jumlah perguruan tinggi mitra
- Jumlah perusahaan mitra
- Kegiatan mitra
- Jumlah lulusan
- Status lulusan
- Pelacakan alumni

### Contoh ringkasan

`Rencana Lulusan Kelas XII`

- Kuliah: 48%
- Kerja: 35%
- Wirausaha: 7%
- Kuliah sambil kerja: 4%
- Belum menentukan: 6%

---

# 29. PEMBUATAN LAPORAN

Guru BK memilih:

`Laporan → Buat Laporan`

Input:
- jenis laporan;
- periode;
- kelas;
- jurusan;
- kategori;
- tujuan laporan;
- format.

Format:
- PDF
- Excel
- CSV bila diperlukan

Sistem membuat:
- judul;
- periode;
- ringkasan;
- tabel;
- grafik;
- tanggal pembuatan;
- pembuat laporan.

---

# 30. PAKET LAPORAN

Sistem menyediakan template siap pakai:

### Paket A — Kesiswaan
`Laporan Perkembangan dan Kondisi Siswa`

### Paket B — Kurikulum
`Laporan Jadwal dan Kegiatan Perguruan Tinggi/Perusahaan`

### Paket C — Kepala Sekolah
`Laporan Eksekutif Bimbingan, Peminatan, Karier dan Lulusan`

Guru BK tidak perlu membuat laporan dari nol.

---

# 31. NOTIFIKASI

## Guru BK menerima

- Pengajuan konseling baru
- Konseling mendekati jadwal
- Tindak lanjut jatuh tempo
- Siswa belum mengisi asesmen
- Siswa kelas akhir belum memiliki rencana
- Jadwal mitra mendekat
- Kerja sama hampir berakhir
- Alumni belum mengisi pelacakan

## Siswa menerima

- Jadwal konseling
- Perubahan jadwal
- Tugas asesmen
- Tindak lanjut yang dapat dilihat
- Peluang baru
- Kegiatan
- Batas pendaftaran
- Informasi perguruan tinggi
- Informasi perusahaan
- Pengumuman BK

---

# 32. PUSAT NOTIFIKASI

Semua notifikasi masuk ke:

`Notifikasi`

Memiliki:
- belum dibaca;
- sudah dibaca;
- tanggal;
- jenis;
- tautan ke data terkait.

Notifikasi tidak boleh memuat isi rahasia konseling pada layar yang dapat terlihat orang lain.

Contoh:

Benar:
> Anda memiliki jadwal layanan BK.

Tidak disarankan:
> Konseling tentang masalah keluarga Anda dijadwalkan...

---

# 33. PENCARIAN GLOBAL

Guru BK membutuhkan pencarian cepat.

Satu kotak pencarian dapat mencari:
- siswa;
- alumni;
- perguruan tinggi;
- perusahaan;
- kegiatan;
- peluang;
- laporan.

Pencarian siswa harus mengikuti hak akses.

---

# 34. FILTER DATA

Semua daftar utama memiliki filter:

- tahun ajaran;
- kelas;
- jurusan;
- status;
- periode;
- kategori;
- tingkat perhatian;
- status layanan;
- status rencana;
- status alumni.

---

# 35. OTOMASI SISTEM

## Otomasi 1 — Siswa baru

Saat data siswa dibuat:
- profil dibuat;
- akun siswa dibuat/diaktifkan;
- dashboard tersedia;
- asesmen wajib dapat diberikan.

## Otomasi 2 — Siswa naik kelas

Sistem memperbarui:
- kelas;
- tahun ajaran;
- status pendidikan.

Riwayat kelas tetap disimpan.

## Otomasi 3 — Siswa kelas akhir

Sistem menampilkan pengingat:

> `Siswa kelas XII belum mengisi Rencana Masa Depan.`

## Otomasi 4 — Konseling

Pengajuan baru → notifikasi Guru BK.

## Otomasi 5 — Tindak lanjut

Tanggal jatuh tempo → masuk daftar pekerjaan Guru BK.

## Otomasi 6 — Jadwal mitra

H-7 → pengingat.

H-1 → pengingat.

## Otomasi 7 — Kerja sama

H-30 sebelum berakhir:
> `Kerja sama dengan Mitra X akan berakhir dalam 30 hari.`

## Otomasi 8 — Alumni

Jadwal pelacakan tiba → sistem mengirim permintaan pembaruan data.

---

# 36. MESIN PRIORITAS PEKERJAAN GURU BK

Beranda Guru BK harus memiliki:

# `YANG PERLU SAYA KERJAKAN`

Urutan default:

1. Konseling yang membutuhkan tindakan.
2. Tindak lanjut yang terlambat.
3. Konseling hari ini.
4. Siswa prioritas.
5. Siswa kelas akhir tanpa rencana.
6. Jadwal mitra terdekat.
7. Peluang yang belum dipublikasikan.
8. Alumni yang perlu dilacak.

Ini adalah salah satu fitur inti untuk benar-benar mengurangi beban administratif Guru BK.

---

# 37. DASHBOARD SISWA

Menu:

- Beranda
- Profil Saya
- Asesmen Saya
- Perkembangan Saya
- Konseling
- Peminatan Saya
- Rencana Masa Depan
- Peluang
- Perguruan Tinggi
- Perusahaan
- Kegiatan Saya
- Alumni (aktif setelah lulus)
- Notifikasi

Dashboard siswa harus sederhana dan tidak terasa seperti aplikasi administrasi sekolah.

---

# 38. LOGIKA "RUTE MASA DEPAN"

Siswa memilih:

`Saya ingin kuliah`

Sistem menampilkan:
- perguruan tinggi;
- program studi;
- beasiswa;
- jalur masuk;
- kegiatan kampus.

Jika:

`Saya ingin bekerja`

Sistem menampilkan:
- perusahaan;
- lowongan;
- magang;
- pelatihan;
- sertifikasi.

Jika:

`Saya ingin wirausaha`

Sistem menampilkan:
- pelatihan;
- kompetisi;
- mentor/kegiatan;
- peluang kewirausahaan.

Jika:

`Belum menentukan`

Sistem menampilkan:
- asesmen;
- eksplorasi minat;
- informasi bidang;
- kegiatan pengenalan karier;
- anjuran berkonsultasi dengan BK.

---

# 39. SISTEM PENANDA STATUS

Gunakan status yang konsisten.

### Status siswa
- Aktif
- Lulus
- Pindah
- Tidak aktif

### Status konseling
- Diajukan
- Ditinjau
- Dijadwalkan
- Dilaksanakan
- Tindak lanjut
- Selesai
- Dialihkan

### Status kegiatan
- Rencana
- Menunggu Konfirmasi
- Terkonfirmasi
- Terlaksana
- Ditunda
- Dibatalkan

### Status peluang
- Draf
- Dipublikasikan
- Ditutup
- Selesai

### Status alumni
- Bekerja
- Kuliah
- Wirausaha
- Mencari pekerjaan
- Belum bekerja
- Belum terlacak

---

# 40. DATA INTI / ENTITAS SISTEM

Database minimal perlu memiliki entitas berikut:

1. Pengguna
2. Guru BK
3. Siswa
4. Tahun Ajaran
5. Kelas
6. Jurusan/Program Keahlian
7. Orang Tua/Wali
8. Riwayat Kelas
9. Prestasi
10. Kegiatan Siswa
11. Asesmen
12. Pertanyaan Asesmen
13. Jawaban Asesmen
14. Hasil Asesmen
15. Pengajuan Konseling
16. Jadwal Konseling
17. Catatan Konseling
18. Tindak Lanjut
19. Pemantauan Siswa
20. Program BK
21. Kegiatan BK
22. Peminatan
23. Rencana Masa Depan
24. Pilihan Perguruan Tinggi
25. Pilihan Pekerjaan
26. Perguruan Tinggi
27. Program Studi
28. Kerja Sama Perguruan Tinggi
29. Perusahaan
30. Kerja Sama Perusahaan
31. Kegiatan Mitra
32. Peluang
33. Pendaftaran Peluang
34. Jadwal
35. Kelulusan
36. Alumni
37. Pelacakan Lulusan
38. Jejak Pendidikan Alumni
39. Jejak Pekerjaan Alumni
40. Notifikasi
41. Laporan
42. Log Aktivitas

Relasi harus menggunakan ID unik dan foreign key yang konsisten.

---

# 41. AUDIT LOG

Setiap aktivitas penting Guru BK dicatat:

- siapa;
- kapan;
- tindakan;
- data yang dipengaruhi;
- status sebelum;
- status sesudah.

Contoh:

`Guru BK A mengubah status konseling BK-00045 dari DIJADWALKAN menjadi DILAKSANAKAN.`

Audit log penting terutama untuk data konseling dan perubahan data sensitif.

---

# 42. NOMOR OTOMATIS

Gunakan nomor dokumen yang mudah dicari.

Contoh:

`KSL-2026-00001` — Konseling

`KGT-2026-00001` — Kegiatan

`MIT-2026-00001` — Mitra

`PEL-2026-00001` — Peluang

`LAP-2026-00001` — Laporan

`ALS-2026-00001` — Alumni

Nomor tidak boleh berubah setelah dibuat.

---

# 43. DASHBOARD ANALITIK

Guru BK dapat melihat:

### Kondisi Siswa
- jumlah;
- distribusi kelas;
- perhatian;
- layanan.

### Peminatan
- minat bidang;
- pilihan kuliah;
- pilihan kerja;
- belum menentukan.

### Mitra
- jumlah perguruan tinggi;
- jumlah perusahaan;
- kegiatan;
- peluang.

### Lulusan
- kuliah;
- kerja;
- wirausaha;
- belum terlacak.

Grafik harus sederhana dan dapat difilter berdasarkan:
- tahun;
- kelas;
- jurusan;
- periode.

---

# 44. VALIDASI FORM

Sistem wajib melakukan validasi:

- NIS/NISN tidak boleh duplikat.
- Email harus valid.
- Nomor telepon harus valid.
- Tanggal selesai tidak boleh sebelum tanggal mulai.
- Jadwal tidak boleh bentrok.
- Kuota tidak boleh negatif.
- Peluang yang sudah ditutup tidak dapat menerima pendaftaran baru.
- Siswa lulus tidak dapat menerima layanan siswa aktif tertentu kecuali diizinkan.
- Data rahasia tidak boleh masuk ke tampilan siswa.

---

# 45. PENCEGAHAN DUPLIKASI

Saat Guru BK memasukkan:
- siswa;
- perguruan tinggi;
- perusahaan;
- alumni;

sistem melakukan pemeriksaan kemiripan.

Contoh:

Guru mengetik:
`Universitas ABC`

Sistem menemukan:

> Data serupa sudah ada: Universitas ABC

Guru memilih:
`Gunakan Data yang Ada`

atau:
`Buat Data Baru`

---

# 46. ALUR UTAMA SISTEM

## ALUR SISWA

`Siswa masuk`
→ `Lengkapi profil`
→ `Asesmen`
→ `Kenali minat`
→ `Lihat perkembangan`
→ `Ajukan konseling jika perlu`
→ `Eksplorasi peluang`
→ `Tentukan rencana masa depan`
→ `Persiapan lulus`
→ `Lulus`
→ `Menjadi alumni`
→ `Pelacakan lulusan`

## ALUR GURU BK

`Masuk`
→ `Beranda`
→ `Lihat pekerjaan prioritas`
→ `Tindak lanjuti siswa`
→ `Kelola konseling`
→ `Pantau perkembangan`
→ `Kelola peminatan`
→ `Kelola peluang`
→ `Kelola kegiatan`
→ `Kelola kelulusan`
→ `Lacak alumni`
→ `Buat laporan`

---

# 47. LAPORAN DAN DISTRIBUSI INFORMASI

Karena hanya ada dua role, distribusi laporan dilakukan sebagai **dokumen keluaran**.

## Guru BK → Wakasek Kesiswaan

Fokus:
- kondisi siswa;
- pembinaan;
- kegiatan BK;
- data agregat siswa.

## Guru BK → Wakasek Kurikulum

Fokus:
- jadwal perguruan tinggi;
- jadwal perusahaan;
- kegiatan mitra;
- kelas yang terlibat;
- benturan jadwal.

## Guru BK → Kepala Sekolah

Fokus:
- kondisi keseluruhan siswa;
- efektivitas layanan;
- peminatan;
- mitra;
- kegiatan;
- rencana lulusan;
- hasil pelacakan lulusan.

---

# 48. FITUR EKSPOR

Setiap laporan dapat:
- ditampilkan;
- dicetak;
- diekspor PDF;
- diekspor Excel;
- diekspor CSV untuk data tabular.

File laporan harus memiliki:
- nama sekolah;
- judul;
- periode;
- tanggal pembuatan;
- pembuat;
- ringkasan;
- data;
- catatan kerahasiaan bila diperlukan.

---

# 49. PRIORITAS DEVELOPMENT

## FASE 1 — FONDASI

- Login
- Role Guru BK
- Role Siswa
- Data siswa
- Tahun ajaran
- Kelas
- Jurusan
- Profil siswa
- Dashboard dasar
- Hak akses
- Notifikasi

## FASE 2 — INTI BK

- Asesmen
- Konseling
- Jadwal
- Catatan
- Tindak lanjut
- Pemantauan
- Perkembangan siswa

## FASE 3 — MASA DEPAN

- Peminatan
- Rencana masa depan
- Perguruan tinggi
- Program studi
- Perusahaan
- Peluang
- Beasiswa
- Magang
- Kegiatan mitra

## FASE 4 — KELULUSAN & ALUMNI

- Kelulusan
- Alumni
- Pelacakan 3/6/12/24 bulan
- Jejak pendidikan
- Jejak pekerjaan

## FASE 5 — LAPORAN

- Laporan Kesiswaan
- Laporan Kurikulum
- Laporan Kepala Sekolah
- PDF
- Excel
- Dashboard analitik

## FASE 6 — PENYEMPURNAAN

- Audit log
- Optimasi pencarian
- Validasi
- Pengingat otomatis
- Rekomendasi peluang
- Peningkatan UX

---

# 50. FITUR YANG TIDAK BOLEH DIABAIKAN

1. Hak akses per data.
2. Privasi catatan konseling.
3. Audit log.
4. Riwayat perubahan data.
5. Backup data.
6. Validasi duplikasi.
7. Jadwal anti-bentrok.
8. Notifikasi tindak lanjut.
9. Riwayat siswa dari kelas ke kelas.
10. Riwayat alumni setelah lulus.
11. Export laporan.
12. Filter berdasarkan periode.
13. Status yang konsisten.
14. Nomor transaksi otomatis.
15. Data mitra tidak boleh sekadar menjadi daftar kontak.
16. Peluang harus dapat dihubungkan dengan mitra.
17. Kegiatan harus dapat dihubungkan dengan kelas.
18. Rencana masa depan harus dapat diperbarui sepanjang waktu.
19. Perubahan rencana siswa harus memiliki riwayat.
20. Semua catatan sensitif harus memiliki tingkat kerahasiaan.

---

# 51. PRINSIP UX

## Untuk Guru BK

Tujuan setiap halaman:

> **"Apa yang perlu saya kerjakan?"**

Bukan:

> "Berapa banyak data yang bisa saya lihat?"

Gunakan:
- tombol tindakan jelas;
- filter cepat;
- pencarian;
- status;
- daftar prioritas;
- formulir bertahap;
- template;
- pengisian otomatis.

## Untuk Siswa

Tujuan setiap halaman:

> **"Apa yang bisa saya lakukan untuk masa depan saya?"**

Gunakan:
- bahasa sederhana;
- kartu informasi;
- progress;
- rekomendasi;
- tombol tindakan;
- notifikasi;
- sedikit formulir;
- tampilan ramah remaja.

---

# 52. PRINSIP DATA

Sistem harus mengikuti:

> **INPUT SEKALI → DIGUNAKAN BERULANG**

Contoh:

Data siswa yang sudah diinput digunakan untuk:
- profil;
- konseling;
- asesmen;
- laporan;
- peminatan;
- kelulusan;
- alumni.

Data perguruan tinggi yang sudah dibuat digunakan untuk:
- mitra;
- kegiatan;
- peluang;
- rencana kuliah;
- laporan.

Data perusahaan yang sudah dibuat digunakan untuk:
- mitra;
- kegiatan;
- magang;
- lowongan;
- rencana kerja;
- laporan.

---

# 53. DEFINISI PRODUK FINAL

Nama generik sistem:

**SISTEM INFORMASI BIMBINGAN, PENGEMBANGAN SISWA & KARIER**

Fungsi inti:

> **Mengelola perjalanan siswa dari awal sekolah sampai menjadi alumni, menyediakan layanan BK digital, membantu siswa mengenali potensi dan menentukan masa depan, menghubungkan siswa dengan perguruan tinggi dan dunia kerja mitra sekolah, serta menyediakan data dan laporan strategis untuk kebutuhan sekolah.**

## Nilai sistem

### Bagi Guru BK
**Lebih cepat mengelola, lebih mudah memantau, lebih terstruktur dalam mendampingi.**

### Bagi Siswa
**Lebih mudah mengenali diri, berkonsultasi, mencari peluang, dan merencanakan masa depan.**

### Bagi Sekolah
**Lebih mudah memahami kondisi siswa, mengelola mitra, mengatur kegiatan, dan mengetahui hasil lulusan.**

### Bagi Alumni
**Tetap terhubung dengan sekolah dan dapat menjadi bagian dari ekosistem peluang bagi generasi berikutnya.**

---

# 54. KALIMAT PRODUK

> **Bukan sekadar sistem administrasi BK, tetapi ekosistem digital yang menghubungkan siswa, layanan BK, peminatan, pendidikan tinggi, dunia kerja, sekolah dan alumni dalam satu perjalanan pengembangan siswa.**

---

# 55. INSTRUKSI IMPLEMENTASI UNTUK BASE44

Saat blueprint ini digunakan sebagai spesifikasi awal pembangunan:

1. Buat aplikasi web responsif.
2. Gunakan Bahasa Indonesia untuk seluruh antarmuka.
3. Buat tepat 2 role: `Guru BK` dan `Siswa`.
4. Jangan membuat role login Wakasek/Kepala Sekolah pada versi ini.
5. Gunakan database relasional dengan ID unik.
6. Terapkan row-level access berdasarkan pengguna.
7. Siswa hanya dapat membaca/mengubah data miliknya yang diizinkan.
8. Guru BK dapat mengelola data sesuai kewenangan.
9. Pisahkan catatan konseling rahasia dari data yang dapat dilihat siswa.
10. Semua perubahan data sensitif dicatat pada audit log.
11. Gunakan status workflow, bukan hanya field teks.
12. Gunakan notifikasi untuk pekerjaan yang membutuhkan tindakan.
13. Sediakan dashboard berbeda untuk Guru BK dan Siswa.
14. Sediakan generator laporan untuk Kesiswaan, Kurikulum dan Kepala Sekolah.
15. Laporan dibuat oleh Guru BK dan dapat diekspor sebagai PDF/Excel.
16. Sediakan data perguruan tinggi, perusahaan dan kerja sama.
17. Hubungkan mitra dengan kegiatan dan peluang.
18. Sediakan pencegahan benturan jadwal.
19. Sediakan rencana masa depan siswa yang dapat berubah dengan riwayat.
20. Sediakan modul alumni dan pelacakan lulusan.
21. Pastikan aplikasi tetap berguna meskipun sebagian data belum lengkap.
22. Hindari fitur yang membuat keputusan psikologis/karier secara otomatis; sistem hanya membantu menyediakan informasi dan prioritas untuk ditinjau Guru BK.
23. Gunakan desain yang bersih, sederhana dan mudah dipahami Guru BK serta siswa.
24. Semua tanggal menggunakan zona waktu sekolah/Indonesia.
25. Sediakan pencarian dan filter pada seluruh data utama.
26. Jangan membuat data dummy permanen pada produksi; gunakan seed/demo data yang dapat dihapus.
27. Pastikan semua data yang dihapus secara logis tetap dapat ditelusuri jika dibutuhkan melalui audit log.
28. Siapkan struktur aplikasi agar fitur tambahan dapat dikembangkan tanpa mengubah fondasi data utama.

---

# 56. KRITERIA SELESAI (DEFINITION OF DONE)

Sistem versi pertama dianggap siap digunakan jika:

- Guru BK dapat login.
- Siswa dapat login.
- Guru BK dapat mengelola siswa.
- Siswa dapat melihat/melengkapi profilnya.
- Siswa dapat mengajukan konseling.
- Guru BK dapat memproses pengajuan.
- Guru BK dapat menjadwalkan konseling.
- Catatan rahasia tidak terlihat siswa.
- Guru BK dapat membuat tindak lanjut.
- Sistem mengingatkan tindak lanjut.
- Siswa dapat mengisi asesmen.
- Guru BK dapat melihat hasil asesmen.
- Siswa dapat membuat rencana masa depan.
- Guru BK dapat memantau rencana tersebut.
- Perguruan tinggi dapat dikelola.
- Perusahaan dapat dikelola.
- Mitra dapat memiliki kegiatan.
- Siswa dapat melihat peluang.
- Siswa dapat mendaftar kegiatan.
- Sistem dapat mendeteksi benturan jadwal.
- Guru BK dapat mengelola kelulusan.
- Lulusan dapat menjadi alumni.
- Alumni dapat diperbarui.
- Pelacakan lulusan dapat dilakukan berkala.
- Guru BK dapat membuat laporan Kesiswaan.
- Guru BK dapat membuat laporan Kurikulum.
- Guru BK dapat membuat laporan Kepala Sekolah.
- Laporan dapat diekspor.
- Hak akses berfungsi.
- Audit log berjalan.
- Data sensitif terlindungi.
- Tidak ada data siswa yang dapat dilihat siswa lain.
- Tidak ada akun Wakasek/Kepala Sekolah yang diperlukan untuk menjalankan sistem versi ini.

---

# 57. ARAH PENGEMBANGAN LANJUTAN

Setelah versi inti stabil, sistem dapat dikembangkan dengan:

- integrasi WhatsApp/email jika tersedia;
- tanda tangan/dokumen digital;
- integrasi data akademik;
- integrasi absensi;
- portal orang tua;
- portal mitra;
- rekomendasi peluang berbasis data;
- analitik lulusan;
- survei kepuasan layanan BK;
- pemetaan kompetensi;
- integrasi sertifikasi;
- integrasi penerimaan perguruan tinggi;
- integrasi lowongan kerja;
- kecerdasan buatan sebagai asisten administratif Guru BK.

Fitur AI harus ditempatkan sebagai **asisten**, bukan pengganti keputusan Guru BK.

---

# PENUTUP

Sistem ini dirancang dengan prinsip:

**KENALI SISWA**  
→ **PAHAMI KEBUTUHANNYA**  
→ **BERIKAN PENDAMPINGAN**  
→ **KEMBANGKAN POTENSINYA**  
→ **BANTU MENENTUKAN MASA DEPAN**  
→ **HUBUNGKAN DENGAN PELUANG**  
→ **PANTAU SETELAH LULUS**

Sehingga hubungan sekolah tidak berhenti pada:

`Siswa → Lulus`

tetapi menjadi:

`Siswa → Berkembang → Lulus → Alumni → Terhubung → Memberi Dampak`

**END OF BLUEPRINT**
