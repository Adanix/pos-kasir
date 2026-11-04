@extends('layouts.admin')

@section('title', 'Absensi Kehadiran Saya')
@section('page_title', 'Absensi Kehadiran Karyawan')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Digital Clock & Today's Attendance Card -->
    <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl p-6 sm:p-8 text-white shadow-lg space-y-6" x-data="{
        time: '',
        date: '',
        updateClock() {
            const now = new Date();
            this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
            this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
    }" x-init="updateClock(); setInterval(() => updateClock(), 1000)">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Sistem Absensi Real-Time
                </span>
                <h2 class="text-xl sm:text-2xl font-bold mt-2">Halo, {{ $user->name }}!</h2>
                <p class="text-xs text-slate-300">NIP: {{ $user->employee_code ?? 'EMP-00' }} &bull; Peran: <span class="capitalize font-semibold text-white">{{ $user->role }}</span></p>
            </div>

            <!-- Digital Clock Display -->
            <div class="text-right sm:text-right bg-white/5 p-3 rounded-xl border border-white/10 shrink-0">
                <div class="text-2xl sm:text-3xl font-mono font-extrabold text-emerald-400" x-text="time">--:--:-- WIB</div>
                <div class="text-[11px] text-slate-300 font-medium" x-text="date">Memuat tanggal...</div>
            </div>
        </div>

        <!-- Action Card: Clock In / Out -->
        <div class="pt-4 border-t border-white/10">
            @if(!$todayAttendance)
            <!-- Belum Absen Masuk -->
            <form action="{{ route('attendance.clock-in') }}" method="POST" class="bg-white/5 p-4 rounded-xl border border-white/10 space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="font-bold text-white text-sm">Anda Belum Absen Masuk Hari Ini</div>
                        <div class="text-slate-300">Pilih shift kerja Anda dan klik tombol di samping untuk mencatat kehadiran.</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <select name="shift_name" class="bg-slate-800 text-white border-slate-700 text-xs rounded-xl focus:ring-emerald-500">
                            <option value="Pagi" {{ $user->default_shift === 'pagi' ? 'selected' : '' }}>Shift Pagi (08:00 - 16:00)</option>
                            <option value="Sore" {{ $user->default_shift === 'sore' ? 'selected' : '' }}>Shift Sore (15:00 - 22:00)</option>
                        </select>
                        <button type="submit" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-emerald-500 hover:bg-emerald-400 text-slate-900 shadow-md transition whitespace-nowrap">
                            Absen Masuk Sekarang &rarr;
                        </button>
                    </div>
                </div>
            </form>
            @elseif(!$todayAttendance->clock_out_at)
            <!-- Sudah Masuk, Belum Pulang -->
            <form action="{{ route('attendance.clock-out') }}" method="POST" class="bg-white/5 p-4 rounded-xl border border-white/10 space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div>
                        <div class="font-bold text-emerald-300 text-sm flex items-center gap-1.5">
                            <span>✓ Telah Absen Masuk</span>
                            <span class="text-xs text-white">({{ $todayAttendance->clock_in_at->format('H:i') }} WIB)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $todayAttendance->status_badge['class'] }}">
                                {{ $todayAttendance->status_badge['label'] }}
                            </span>
                        </div>
                        <div class="text-slate-300 mt-0.5">Shift: {{ $todayAttendance->shift_name }} ({{ substr($todayAttendance->scheduled_start, 0, 5) }} - {{ substr($todayAttendance->scheduled_end, 0, 5) }})</div>
                    </div>
                    <div>
                        <button type="submit" class="py-2.5 px-6 rounded-xl font-bold text-xs bg-amber-500 hover:bg-amber-400 text-slate-900 shadow-md transition whitespace-nowrap">
                            Absen Pulang (Clock-Out) &rarr;
                        </button>
                    </div>
                </div>
            </form>
            @else
            <!-- Selesai Absen Masuk & Pulang -->
            <div class="bg-white/5 p-4 rounded-xl border border-white/10 flex items-center justify-between text-xs">
                <div>
                    <div class="font-bold text-emerald-400 text-sm">Kehadiran Hari Ini Lengkap & Selesai</div>
                    <div class="text-slate-300 mt-0.5">
                        Masuk: {{ $todayAttendance->clock_in_at->format('H:i') }} WIB &bull; 
                        Pulang: {{ $todayAttendance->clock_out_at->format('H:i') }} WIB &bull; 
                        Durasi Kerja: {{ floor($todayAttendance->work_duration_minutes / 60) }} jam {{ $todayAttendance->work_duration_minutes % 60 }} menit
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Selesai
                </span>
            </div>
            @endif
        </div>
    </div>

    <!-- Personal Attendance History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm">Riwayat Absensi Pribadi (15 Hari Terakhir)</h3>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Absen Masuk</th>
                        <th class="py-3 px-4">Absen Pulang</th>
                        <th class="py-3 px-4 text-center">Durasi Kerja</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentAttendances as $att)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-medium text-slate-800 whitespace-nowrap">
                            {{ format_tanggal($att->attendance_date, false) }}
                        </td>
                        <td class="py-3 px-4 text-slate-700">
                            {{ $att->shift_name }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-700 whitespace-nowrap">
                            {{ $att->clock_in_at ? $att->clock_in_at->format('H:i') . ' WIB' : '-' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-700 whitespace-nowrap">
                            {{ $att->clock_out_at ? $att->clock_out_at->format('H:i') . ' WIB' : '-' }}
                        </td>
                        <td class="py-3 px-4 text-center font-mono whitespace-nowrap">
                            @if($att->work_duration_minutes > 0)
                                {{ floor($att->work_duration_minutes / 60) }}j {{ $att->work_duration_minutes % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $att->status_badge['class'] }}">
                                {{ $att->status_badge['label'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 max-w-xs truncate">
                            {{ $att->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat absensi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
