@extends('layouts.pos')

@section('title', 'Manajemen Shift Kasir')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 max-w-6xl mx-auto w-full space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Manajemen Shift & Kas Laci</h1>
            <p class="text-xs sm:text-sm text-slate-400">Buka shift kasir, catat pengeluaran kas kecil laci, dan tutup shift harian.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition">
            &larr; Kembali ke Kasir
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-500/50 rounded-2xl text-xs text-emerald-200 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-950/60 border border-rose-500/50 rounded-2xl text-xs text-rose-200 flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($activeShift)
        <!-- ACTIVE SHIFT SUMMARY -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left: Shift Financial Overview -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                            <h2 class="text-lg font-bold text-white">Shift {{ $activeShift->shift_name }} (Sedang Aktif)</h2>
                        </div>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $activeShift->shift_code }} &bull; Dibuka {{ format_tanggal($activeShift->opened_at, 'd M Y H:i:s') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 text-xs font-semibold">
                        Status Terbuka
                    </span>
                </div>

                <!-- Ledger Counters -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-slate-800/60 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Modal Awal Kas</span>
                        <div class="text-base font-bold font-mono text-slate-200 mt-1">{{ format_rupiah($activeShift->opening_cash) }}</div>
                    </div>
                    <div class="p-4 bg-slate-800/60 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Penjualan Tunai</span>
                        <div class="text-base font-bold font-mono text-emerald-400 mt-1">{{ format_rupiah($activeShift->cash_sales_total) }}</div>
                    </div>
                    <div class="p-4 bg-slate-800/60 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-sky-400 tracking-wider">Penjualan Non-Tunai</span>
                        <div class="text-base font-bold font-mono text-sky-400 mt-1">{{ format_rupiah($activeShift->non_cash_sales_total) }}</div>
                    </div>
                    <div class="p-4 bg-slate-800/60 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-rose-400 tracking-wider">Kas Kecil Keluar</span>
                        <div class="text-base font-bold font-mono text-rose-400 mt-1">{{ format_rupiah($activeShift->petty_cash_out_total) }}</div>
                    </div>
                </div>

                <div class="p-4 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400">Perkiraan Uang Tunai di Laci Kasir:</span>
                        <div class="text-2xl font-black font-mono text-emerald-400 mt-0.5">{{ format_rupiah($activeShift->closing_cash_expected) }}</div>
                        <span class="text-[11px] text-slate-500">(Modal Awal + Penjualan Tunai - Kas Keluar)</span>
                    </div>
                </div>

                <!-- Petty Cash Drawer Expense Form -->
                <div class="pt-4 border-t border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Catat Kas Keluar dari Laci (Petty Cash)</span>
                    </h3>
                    <form method="POST" action="{{ route('pos.shift.expense') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @csrf
                        <div>
                            <input type="number" name="amount" min="1000" placeholder="Nominal (Rp)" required class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white font-mono focus:border-amber-500">
                        </div>
                        <div>
                            <input type="text" name="description" placeholder="Keperluan (cth: Beli es batu, kresek)" required class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-amber-500">
                        </div>
                        <div>
                            <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold rounded-xl shadow transition">
                                Simpan Kas Keluar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Close Shift Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4" x-data="{ actualCash: {{ $activeShift->closing_cash_expected }}, expectedCash: {{ $activeShift->closing_cash_expected }} }">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-base font-bold text-white">Tutup Shift Kasir</h3>
                    <p class="text-xs text-slate-400">Hitung fisik uang tunai di laci dan serahkan laporan kasir.</p>
                </div>

                <form method="POST" action="{{ route('pos.shift.close') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Total Fisik Uang di Laci (Rp)</label>
                        <input type="number" 
                               name="closing_cash_actual" 
                               x-model.number="actualCash" 
                               required 
                               class="w-full text-sm font-mono font-bold py-2.5 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500">
                    </div>

                    <!-- Selisih Kas -->
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Selisih Fisik vs Sistem:</span>
                            <span class="font-mono font-bold text-sm" 
                                  :class="(actualCash - expectedCash) === 0 ? 'text-emerald-400' : ((actualCash - expectedCash) > 0 ? 'text-sky-400' : 'text-rose-400')"
                                  x-text="'Rp ' + Number(actualCash - expectedCash).toLocaleString('id-ID')">
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Penutupan Shift</label>
                        <textarea name="notes" rows="2" placeholder="Catatan operasional selama shift..." class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500"></textarea>
                    </div>

                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menutup shift kasir saat ini?')" class="w-full py-3 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow transition">
                        Tutup Shift & Kunci Transaksi
                    </button>
                </form>
            </div>

        </div>
    @else
        <!-- OPEN NEW SHIFT FORM -->
        <div class="max-w-xl mx-auto bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="text-center space-y-1">
                <div class="w-12 h-12 rounded-2xl bg-emerald-950 text-emerald-400 border border-emerald-800 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-white">Buka Shift Kasir Baru</h2>
                <p class="text-xs text-slate-400">Masukkan modal awal uang kembalian untuk memulai transaksi penjualan.</p>
            </div>

            <form method="POST" action="{{ route('pos.shift.open') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Shift Kerja</label>
                    <select name="shift_name" required class="w-full text-xs py-2.5 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500">
                        <option value="Pagi">Shift Pagi (08:00 - 16:00)</option>
                        <option value="Sore">Shift Sore (15:00 - 22:00)</option>
                        <option value="Malam">Shift Malam</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Modal Awal Kas Laci (Rp)</label>
                    <input type="number" name="opening_cash" value="200000" min="0" required class="w-full text-sm font-mono font-bold py-2.5 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Uang pecahan kecil untuk persediaan kembalian pelanggan.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan pembukaan shift..." class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-950 transition">
                    Buka Shift & Masuk ke Kasir
                </button>
            </form>
        </div>
    @endif

    <!-- PAST SHIFTS TABLE -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Riwayat Shift Sebelumnya</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] font-semibold">
                        <th class="py-3 px-3">Kode Shift</th>
                        <th class="py-3 px-3">Shift</th>
                        <th class="py-3 px-3">Waktu Buka / Tutup</th>
                        <th class="py-3 px-3 text-right">Modal Awal</th>
                        <th class="py-3 px-3 text-right">Penjualan Tunai</th>
                        <th class="py-3 px-3 text-right">Kas Fisik Akhir</th>
                        <th class="py-3 px-3 text-right">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($pastShifts as $ps)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3 px-3 font-mono text-slate-400">{{ $ps->shift_code }}</td>
                            <td class="py-3 px-3 font-semibold text-white">{{ $ps->shift_name }}</td>
                            <td class="py-3 px-3 text-slate-400">
                                <div>{{ format_tanggal($ps->opened_at, 'd/m H:i') }} - {{ format_tanggal($ps->closed_at, 'H:i') }}</div>
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-slate-300">{{ format_rupiah($ps->opening_cash) }}</td>
                            <td class="py-3 px-3 text-right font-mono text-emerald-400">{{ format_rupiah($ps->cash_sales_total) }}</td>
                            <td class="py-3 px-3 text-right font-mono text-white font-bold">{{ format_rupiah($ps->closing_cash_actual) }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold whitespace-nowrap {{ $ps->difference_amount == 0 ? 'text-emerald-400' : ($ps->difference_amount > 0 ? 'text-sky-400' : 'text-rose-400') }}">
                                {{ format_rupiah($ps->difference_amount) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">Belum ada riwayat penutupan shift.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
