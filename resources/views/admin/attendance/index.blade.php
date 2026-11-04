@extends('layouts.admin')

@section('title', 'Rekap Absensi Staf')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Rekap Absensi Staf</h1>
            <p class="text-sm text-slate-500">Monitoring kehadiran, keterlambatan, dan pencatatan izin / cuti staf operasional.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="addModalOpen = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Izin / Koreksi</span>
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Hadir Tepat Waktu</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ $totalHadir }}</div>
                <span class="text-[11px] text-slate-500">Sesuai jam operasional shift</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Terlambat</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $totalTerlambat }}</div>
                <span class="text-[11px] text-slate-500">Masuk setelah jadwal shift</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Izin / Sakit</span>
                <div class="text-2xl font-black text-indigo-600 mt-1">{{ $totalIzinSakit }}</div>
                <span class="text-[11px] text-slate-500">Tercatat keterangan cuti/sakit</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filters Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Karyawan</label>
                <select name="user_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ $userId == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }} ({{ strtoupper($staff->role) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                <select name="status" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    <option value="">-- Semua Status --</option>
                    <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ $status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ $status == 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Jam Pulang</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $row)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-medium text-slate-700 whitespace-nowrap">
                                {{ format_tanggal($row->attendance_date, 'd M Y') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $row->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $row->user->employee_code ?? '' }} &bull; {{ strtoupper($row->user->role ?? '') }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-600 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $row->shift_name }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-800 whitespace-nowrap">
                                {{ $row->clock_in_at ? \Carbon\Carbon::parse($row->clock_in_at)->format('H:i:s') : '—' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-800 whitespace-nowrap">
                                {{ $row->clock_out_at ? \Carbon\Carbon::parse($row->clock_out_at)->format('H:i:s') : '—' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($row->status === 'hadir')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Hadir
                                    </span>
                                @elseif($row->status === 'terlambat')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Terlambat
                                    </span>
                                @elseif($row->status === 'izin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                        Izin
                                    </span>
                                @elseif($row->status === 'sakit')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-800">
                                        Sakit
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                                        Alpha
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $row->notes ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                Belum ada riwayat absensi pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

    <!-- Manual Attendance Entry Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="addModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Input Absensi / Izin Staf</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Karyawan</label>
                    <select name="user_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ strtoupper($staff->role) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="attendance_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Shift</label>
                    <select name="shift_name" required class="w-full text-xs rounded-xl border-slate-200">
                        <option value="Pagi">Shift Pagi (08:00 - 16:00)</option>
                        <option value="Sore">Shift Sore (15:00 - 22:00)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                    <select name="status" required class="w-full text-xs rounded-xl border-slate-200">
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="hadir">Hadir (Koreksi Jam)</option>
                        <option value="alpha">Alpha</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Alasan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Izin urusan keluarga mendadak" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
