@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Laba Kotor')
@section('page_title', 'Laporan Omzet, HPP & Laba Kotor')

@section('content')
<div class="space-y-6">

    <!-- Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 uppercase text-[10px] mb-1">Periode Awal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-xl border-slate-300 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 uppercase text-[10px] mb-1">Periode Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-xl border-slate-300 text-xs focus:ring-emerald-500">
            </div>
            <div>
                <button type="submit" class="py-2.5 px-4 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                    Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Omzet -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <div class="text-xs text-slate-500">Total Pendapatan (Omzet)</div>
            <div class="text-xl font-extrabold text-slate-900 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($totalRevenue) }}
            </div>
            <div class="text-[11px] text-slate-400">{{ $totalOrdersCount }} transaksi terbayar</div>
        </div>

        <!-- Total HPP -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <div class="text-xs text-slate-500">Total HPP (Harga Modal)</div>
            <div class="text-xl font-bold text-slate-700 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($totalHpp) }}
            </div>
            <div class="text-[11px] text-slate-400">Total biaya modal barang</div>
        </div>

        <!-- Laba Kotor -->
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-sm space-y-1">
            <div class="text-xs text-emerald-800 font-semibold">Laba Kotor (Gross Profit)</div>
            <div class="text-xl font-extrabold text-emerald-700 font-mono tracking-tight whitespace-nowrap">
                {{ format_rupiah($grossProfit) }}
            </div>
            <div class="text-[11px] text-emerald-600">Margin: {{ $profitMargin }}%</div>
        </div>

        <!-- Kanal Breakdown -->
        <div class="p-5 rounded-2xl bg-indigo-50 border border-indigo-200 shadow-sm space-y-1">
            <div class="text-xs text-indigo-800 font-semibold">Perbandingan Kanal</div>
            <div class="text-xs text-slate-700 space-y-1 pt-1">
                <div class="flex justify-between">
                    <span>Kasir POS:</span>
                    <strong class="font-mono text-emerald-700">{{ format_rupiah($posRevenue) }} ({{ $posCount }})</strong>
                </div>
                <div class="flex justify-between">
                    <span>Toko Online:</span>
                    <strong class="font-mono text-indigo-700">{{ format_rupiah($onlineRevenue) }} ({{ $onlineCount }})</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-sm">Produk Terlaris pada Periode Ini</h3>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase font-semibold">
                    <tr>
                        <th class="py-3 px-4">Kode SKU</th>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4 text-center">Qty Terjual</th>
                        <th class="py-3 px-4 text-right">Total Penjualan</th>
                        <th class="py-3 px-4 text-right">Total Modal (HPP)</th>
                        <th class="py-3 px-4 text-right">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topProducts as $tp)
                    @php
                        $itemProfit = $tp->total_sales - $tp->total_cost;
                    @endphp
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-3 px-4 font-mono font-medium text-slate-700">{{ $tp->sku_snapshot }}</td>
                        <td class="py-3 px-4 font-semibold text-slate-900">{{ $tp->product_name_snapshot }}</td>
                        <td class="py-3 px-4 text-center font-bold font-mono">{{ $tp->total_qty }} Pcs</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">{{ format_rupiah($tp->total_sales) }}</td>
                        <td class="py-3 px-4 text-right font-mono text-slate-500">{{ format_rupiah($tp->total_cost) }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600">{{ format_rupiah($itemProfit) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data penjualan pada rentang tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
