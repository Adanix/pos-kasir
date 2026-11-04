@extends('layouts.pos')

@section('title', 'Riwayat Transaksi Kasir POS')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 max-w-6xl mx-auto w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Riwayat Struk & Penjualan POS</h1>
            <p class="text-xs sm:text-sm text-slate-400">Daftar transaksi kasir walk-in harian beserta opsi cetak ulang struk thermal.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl transition">
            &larr; Buka Terminal Kasir
        </a>
    </div>

    <!-- Daily Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Omset Tanggal Ini</span>
                <div class="text-2xl font-black font-mono text-emerald-400 mt-1">{{ format_rupiah($totalSales) }}</div>
                <span class="text-[11px] text-slate-500">Dari seluruh penjualan kasir</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-950 text-emerald-400 border border-emerald-800 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Transaksi Struk</span>
                <div class="text-2xl font-black font-mono text-white mt-1">{{ $totalOrders }} struk</div>
                <span class="text-[11px] text-slate-500">Transaksi selesai tercatat</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-300 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4">
        <form method="GET" action="{{ route('pos.transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Pilih Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Cari No. Struk / Pelanggan</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="ORD-POS..." class="w-full text-xs py-2 px-3 bg-slate-800 border-slate-700 rounded-xl text-white focus:border-emerald-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition">
                    Tampilkan
                </button>
                <a href="{{ route('pos.transactions.index') }}" class="py-2 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                    Hari Ini
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] font-semibold bg-slate-950/40">
                        <th class="py-3.5 px-4">No. Struk & Waktu</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Kasir</th>
                        <th class="py-3.5 px-4">Item Belanja</th>
                        <th class="py-3.5 px-4">Metode Bayar</th>
                        <th class="py-3.5 px-4 text-right">Total Transaksi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-mono font-bold text-white">{{ $ord->order_number }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ format_tanggal($ord->ordered_at, 'H:i:s') }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-300">
                                {{ $ord->customer_name }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400">
                                {{ $ord->cashier->name ?? 'Kasir' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 max-w-xs truncate">
                                {{ $ord->items->pluck('product_name_snapshot')->join(', ') }}
                            </td>
                            <td class="py-3.5 px-4 uppercase text-slate-400 font-mono text-[11px]">
                                {{ $ord->payment_method }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400 text-sm whitespace-nowrap">
                                {{ format_rupiah($ord->grand_total) }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('pos.orders.receipt', ['order' => $ord, 'width' => '58']) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg border border-slate-700 transition">
                                    Struk 58mm
                                </a>
                                <a href="{{ route('pos.orders.receipt', ['order' => $ord, 'width' => '80']) }}" target="_blank" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg border border-slate-700 transition">
                                    80mm
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                Belum ada transaksi penjualan kasir pada tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
