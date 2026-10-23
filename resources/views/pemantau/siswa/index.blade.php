@extends('layouts.pemantau')

@section('title', 'Data Siswa - Ruang BK Digital')

@section('content')
<!-- Page Header Bar -->
<div class="page-header-bar">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <span class="badge badge-primary" style="font-size: 11px; padding: 3px 8px; font-weight: 700;">
                DATA RESMI SEKOLAH
            </span>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Direktori Siswa & Pembagian Pembina BK
            </span>
        </div>
        <h1 class="page-header-title">
            Data Siswa Ruang BK Digital
        </h1>
    </div>
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <a href="{{ route($rolePrefix . '.ekspor.index', request()->query()) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Ekspor Data Siswa</span>
        </a>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header card-header-navy" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 16px 20px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60A5FA" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
            <div>
                <h3 class="card-title" style="font-size: 16px; font-weight: 700; color: #FFFFFF; margin: 0 0 2px 0;">
                    Tabel Direktori Data Siswa
                </h3>
                <span style="font-size: 12.5px; color: rgba(255, 255, 255, 0.75);">
                    Daftar seluruh siswa aktif dengan detail pembagian pembina BK
                </span>
            </div>
        </div>
        <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.25); font-size: 11px; padding: 4px 10px;">
            Total: {{ $students->total() }} Siswa
        </span>
    </div>

    <!-- Filter Section -->
    <div style="padding: 18px 20px; background: var(--color-surface-subtle); border-bottom: 1px solid var(--color-border);">
        <form method="GET" action="{{ route($rolePrefix . '.siswa.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 220px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Kata Kunci Pencarian</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama, NISN, NIS, atau Nomor HP..." style="height: 38px;" />
            </div>

            <div style="width: 130px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Tingkat</label>
                <select name="grade" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Tingkat</option>
                    <option value="X" {{ request('grade') === 'X' ? 'selected' : '' }}>Kelas X</option>
                    <option value="XI" {{ request('grade') === 'XI' ? 'selected' : '' }}>Kelas XI</option>
                    <option value="XII" {{ request('grade') === 'XII' ? 'selected' : '' }}>Kelas XII</option>
                </select>
            </div>

            <div style="min-width: 170px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Jurusan</label>
                <select name="major" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Jurusan</option>
                    @foreach($majors as $m)
                        <option value="{{ $m }}" {{ request('major') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width: 150px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Rombel</label>
                <select name="student_class_id" class="form-control" style="height: 38px;">
                    <option value="semua">Semua Rombel</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('student_class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 120px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: var(--color-text-muted); margin-bottom: 4px;">Gender</label>
                <select name="gender" class="form-control" style="height: 38px;">
                    <option value="semua">Semua</option>
                    <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 38px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <span>Cari</span>
                </button>
                <a href="{{ route($rolePrefix . '.siswa.index') }}" class="btn btn-secondary" style="height: 38px;" title="Reset Filter">
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead class="table-thead-navy">
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama & Foto Siswa</th>
                    <th>NISN & NIS</th>
                    <th>Tingkat & Rombel</th>
                    <th>Jurusan</th>
                    <th>Guru Pembina BK</th>
                    <th>Kontak Siswa</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center; width: 85px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $idx => $st)
                    <tr>
                        <td style="text-align: center; color: var(--color-text-muted); font-size: 12px;">
                            {{ $students->firstItem() + $idx }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ $st->avatar_url }}" alt="{{ $st->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-border);" />
                                <div>
                                    <div style="font-weight: 700; color: var(--color-navy); font-size: 13.5px;">{{ $st->name }}</div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">{{ $st->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 12.5px; font-weight: 600; color: var(--color-navy);">{{ $st->nisn ?? '-' }}</div>
                            <div style="font-size: 11.5px; color: var(--color-text-muted);">NIS: {{ $st->nis ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $st->studentClass->grade === 'X' ? 'badge-success' : ($st->studentClass->grade === 'XI' ? 'badge-warning' : 'badge-primary') }}" style="font-size: 11px;">
                                {{ $st->studentClass->name ?? '-' }}
                            </span>
                        </td>
                        <td style="font-size: 12.5px; color: var(--color-text-navy);">
                            {{ $st->studentClass->major ?? '-' }}
                        </td>
                        <td>
                            @if($st->counselor)
                                <div style="font-weight: 600; font-size: 12.5px; color: var(--color-navy);">{{ $st->counselor->name }}</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">Guru BK Pembina</div>
                            @else
                                <span style="font-size: 12px; color: var(--color-text-muted); font-style: italic;">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td style="font-size: 12.5px; color: var(--color-text-main);">
                            {{ $st->phone ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge {{ $st->status === 'aktif' ? 'badge-success' : 'badge-secondary' }}" style="font-size: 11px;">
                                {{ ucfirst((string) $st->status) }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route($rolePrefix . '.siswa.show', $st->encrypted_id) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 12px;" title="Lihat Profil Siswa">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px 20px;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color: var(--color-text-subtle);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <span style="font-size: 14px; font-weight: 600; color: var(--color-navy);">Data Siswa Tidak Ditemukan</span>
                                <span style="font-size: 12.5px; color: var(--color-text-muted);">Tidak ada data yang cocok dengan kriteria filter saat ini.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->total() > 0)
        {{ $students->links('components.pagination') }}
    @endif
</div>
@endsection
