@extends('layouts.admin')

@section('title', 'Buku Kas Harian (Daily Cashbook)')
@section('page_title', 'Buku Kas Harian & Arus Keuangan Toko')

@section('content')
<div class="space-y-6" x-data="{ modalOpen: false }">

    <!-- Top Summary Banner: 4 Financial Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Saldo Awal -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <div class="text-xs text-slate-500 font-medium">Saldo Awal Periode</div>
            <div class="text-lg sm:text-xl font-bold text-slate-700 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($summary['opening_balance']) }}
            </div>
            <div class="text-[11px] text-slate-400">Kumulatif s.d. sebelum {{ format_tanggal($startDate, false) }}</div>
        </div>

        <!-- Total Pemasukan -->
        <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200 shadow-sm space-y-1">
            <div class="text-xs text-emerald-800 font-semibold flex items-center justify-between">
                <span>Total Kas Masuk (+)</span>
                <span class="text-emerald-600">↓ Masuk</span>
            </div>
            <div class="text-lg sm:text-xl font-bold text-emerald-700 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($summary['total_in']) }}
            </div>
            <div class="text-[11px] text-emerald-600">Penjualan kasir, online & modal</div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="p-5 rounded-2xl bg-rose-50/60 border border-rose-200 shadow-sm space-y-1">
            <div class="text-xs text-rose-800 font-semibold flex items-center justify-between">
                <span>Total Kas Keluar (-)</span>
                <span class="text-rose-600">↑ Keluar</span>
            </div>
            <div class="text-lg sm:text-xl font-bold text-rose-700 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($summary['total_out']) }}
            </div>
            <div class="text-[11px] text-rose-600">Operasional, beli stok & gaji</div>
        </div>

        <!-- Saldo Akhir -->
        <div class="p-5 rounded-2xl bg-indigo-50/60 border border-indigo-200 shadow-sm space-y-1">
            <div class="text-xs text-indigo-800 font-semibold flex items-center justify-between">
                <span>Saldo Akhir Periode</span>
                <span class="text-indigo-600">Saldo Netto</span>
            </div>
            <div class="text-lg sm:text-xl font-extrabold text-indigo-900 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($summary['closing_balance']) }}
            </div>
            <div class="text-[11px] text-indigo-700">Saldo kas riil per {{ format_tanggal($endDate, false) }}</div>
        </div>
    </div>

    <!-- Filter & Action Header -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <form action="{{ route('admin.cashbook.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Tipe Transaksi</label>
                <select name="type" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Tipe (Masuk & Keluar)</option>
                    <option value="in" {{ $type === 'in' ? 'selected' : '' }}>Kas Masuk (+)</option>
                    <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Kas Keluar (-)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-700 uppercase mb-1">Kategori</label>
                <select name="category" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kategori</option>
                    <option value="modal_awal" {{ $category === 'modal_awal' ? 'selected' : '' }}>Modal Awal Kas</option>
                    <option value="penjualan_pos" {{ $category === 'penjualan_pos' ? 'selected' : '' }}>Penjualan Kasir POS</option>
                    <option value="penjualan_online" {{ $category === 'penjualan_online' ? 'selected' : '' }}>Penjualan Toko Online</option>
                    <option value="operasional_toko" {{ $category === 'operasional_toko' ? 'selected' : '' }}>Operasional Toko</option>
                    <option value="pembelian_stok" {{ $category === 'pembelian_stok' ? 'selected' : '' }}>Pembelian Stok</option>
                    <option value="gaji_staf" {{ $category === 'gaji_staf' ? 'selected' : '' }}>Gaji & Uang Makan Staf</option>
                    <option value="refund_penjualan" {{ $category === 'refund_penjualan' ? 'selected' : '' }}>Refund Penjualan</option>
                    <option value="lainnya" {{ $category === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition">
                    Filter
                </button>
                <button type="button" @click="modalOpen = true" class="py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition whitespace-nowrap">
                    + Catat Transaksi
                </button>
            </div>
        </form>
    </div>

    <!-- Ledger Table (Responsive Protection) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Buku Jurnal Kas Harian (Chronological Ledger)</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $entries->total() }} baris transaksi</span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Waktu Transaksi</th>
                        <th class="py-3 px-4">No. Bukti / Ref</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-right">Kas Masuk (+)</th>
                        <th class="py-3 px-4 text-right">Kas Keluar (-)</th>
                        <th class="py-3 px-4 text-right font-mono">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($entries as $e)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">
                            {{ $e->transaction_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4 font-mono font-medium text-slate-800 whitespace-nowrap">
                            {{ $e->entry_number }}
                            @if($e->reference_number)
                                <div class="text-[10px] text-slate-400">Ref: {{ $e->reference_number }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $e->isIn() ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ $e->category_label }}
                            </span>
                            <span class="text-[10px] text-slate-400 uppercase font-mono ml-1">({{ $e->payment_method }})</span>
                        </td>
                        <td class="py-3 px-4 text-slate-800 max-w-xs">
                            <div class="line-clamp-2 leading-relaxed">{{ $e->description }}</div>
                            @if($e->creator)
                                <div class="text-[10px] text-slate-400">Oleh: {{ $e->creator->name }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap text-emerald-600">
                            {{ $e->isIn() ? format_rupiah($e->amount) : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap text-rose-600">
                            {{ $e->isOut() ? format_rupiah($e->amount) : '-' }}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">
                            {{ format_rupiah($e->balance_after) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada transaksi kas pada periode yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $entries->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Input Manual Kas Masuk / Keluar -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="modalOpen = false" class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Catat Transaksi Buku Kas Manual</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('admin.cashbook.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tipe Transaksi</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center p-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold cursor-pointer hover:bg-emerald-100">
                            <input type="radio" name="type" value="in" class="mr-2 text-emerald-600" checked>
                            Kas Masuk (+)
                        </label>
                        <label class="flex items-center justify-center p-2.5 rounded-xl border border-rose-300 bg-rose-50 text-rose-800 font-bold cursor-pointer hover:bg-rose-100">
                            <input type="radio" name="type" value="out" class="mr-2 text-rose-600">
                            Kas Keluar (-)
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kategori</label>
                    <select name="category" required class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="operasional_toko">Biaya Operasional Toko (Kertas, Plastik, Listrik)</option>
                        <option value="pembelian_stok">Pembelian Stok / Restock Bahan</option>
                        <option value="gaji_staf">Gaji / Uang Makan Karyawan</option>
                        <option value="modal_awal">Tambahan Modal Kas Toko</option>
                        <option value="lainnya">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nominal (Rupiah)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 font-mono font-bold">Rp</span>
                        <input type="number" name="amount" min="100" step="100" required placeholder="50000" class="w-full pl-10 text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Metode Bayar</label>
                        <select name="payment_method" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="cash">Tunai (Cash Drawer)</option>
                            <option value="bank_transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Bukti / Ref (Opsional)</label>
                        <input type="text" name="reference_number" placeholder="NOTA-012" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Keterangan / Keperluan</label>
                    <textarea name="description" rows="2" required placeholder="Contoh: Beli 5 roll kertas thermal dan lakban" class="w-full text-xs rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="py-2 px-4 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="py-2 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-sm">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
