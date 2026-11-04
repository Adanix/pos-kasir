@extends('layouts.admin')

@section('title', 'Dashboard Eksekutif')
@section('page_title', 'Dashboard Eksekutif Pemilik')

@section('content')
<!-- KPI Cards Grid (4 columns desktop, 2 col tablet, 1 col mobile) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- KPI 1: Omzet Hari Ini -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs">
            <span>Total Omzet Hari Ini</span>
            <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </span>
        </div>
        <div class="text-xl sm:text-2xl font-extrabold text-slate-900 font-mono tracking-tight whitespace-nowrap">
            {{ format_rupiah($totalRevenueToday) }}
        </div>
        <div class="text-[11px] text-slate-500 flex items-center justify-between pt-1 border-t border-slate-100">
            <span>POS: <strong class="text-slate-800 font-mono">{{ format_rupiah($posRevenueToday) }}</strong></span>
            <span>Online: <strong class="text-indigo-600 font-mono">{{ format_rupiah($onlineRevenueToday) }}</strong></span>
        </div>
    </div>

    <!-- KPI 2: Saldo Buku Kas -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs">
            <span>Saldo Berjalan Buku Kas</span>
            <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </span>
        </div>
        <div class="text-xl sm:text-2xl font-extrabold text-indigo-700 font-mono tracking-tight whitespace-nowrap">
            {{ format_rupiah($currentCashBalance) }}
        </div>
        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 flex items-center justify-between">
            <span>Kas fisik & rekening toko</span>
            <a href="{{ route('admin.cashbook.index') }}" class="text-indigo-600 hover:underline font-semibold">Buka Buku Kas &rarr;</a>
        </div>
    </div>

    <!-- KPI 3: Pesanan Online Masuk -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs">
            <span>Antrean Pesanan Online</span>
            <span class="p-1.5 rounded-lg bg-purple-50 text-purple-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </span>
        </div>
        <div class="text-xl sm:text-2xl font-extrabold text-purple-700 font-mono tracking-tight">
            {{ $pendingOnlineOrdersCount }} <span class="text-xs font-normal text-slate-500">Pesanan</span>
        </div>
        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 flex items-center justify-between">
            <span>Perlu diproses / packing</span>
            <a href="{{ route('pos.online-orders.index') }}" class="text-purple-600 hover:underline font-semibold">Cek Antrean &rarr;</a>
        </div>
    </div>

    <!-- KPI 4: Stok Menipis -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="flex items-center justify-between text-slate-500 text-xs">
            <span>Peringatan Stok Menipis</span>
            <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </span>
        </div>
        <div class="text-xl sm:text-2xl font-extrabold text-amber-600 font-mono tracking-tight">
            {{ $lowStockCount }} <span class="text-xs font-normal text-slate-500">Item Produk</span>
        </div>
        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-100 flex items-center justify-between">
            <span>Stok &le; batas minimum</span>
            <a href="{{ route('warehouse.inventory.index') }}" class="text-amber-700 hover:underline font-semibold">Cek Gudang &rarr;</a>
        </div>
    </div>
</div>

<!-- Grid 2 Columns: Recent Orders & Today's Attendance -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left 7 cols: Transaksi Terakhir (POS & Online) -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Aktivitas Transaksi Terbaru</h3>
            <a href="{{ route('pos.transactions.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Lihat Semua Struk &rarr;</a>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                    <tr>
                        <th class="py-2.5 px-3">No. Pesanan</th>
                        <th class="py-2.5 px-3">Kanal</th>
                        <th class="py-2.5 px-3">Pelanggan</th>
                        <th class="py-2.5 px-3">Total</th>
                        <th class="py-2.5 px-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-2.5 px-3 font-mono font-medium text-slate-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $order->isOnline() ? 'bg-indigo-100 text-indigo-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $order->isOnline() ? 'Toko Online' : 'Kasir POS' }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-slate-700 truncate max-w-[120px]">
                            {{ $order->customer_name }}
                        </td>
                        <td class="py-2.5 px-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            {{ format_rupiah($order->grand_total) }}
                        </td>
                        <td class="py-2.5 px-3 text-right">
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $order->status_badge['class'] }}">
                                {{ $order->status_badge['label'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-slate-400">Belum ada transaksi hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right 5 cols: Staff Attendance Today -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 flex flex-col justify-between">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm">Kehadiran Staf Hari Ini</h3>
                <a href="{{ route('admin.attendance.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Rekap Bulanan &rarr;</a>
            </div>

            <div class="space-y-2.5">
                @forelse($todayAttendances as $att)
                <div class="p-3 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                            {{ substr($att->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900">{{ $att->user->name }}</div>
                            <div class="text-[10px] text-slate-500">{{ ucfirst($att->user->role) }} &bull; Shift {{ $att->shift_name }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $att->status_badge['class'] }}">
                            {{ $att->status_badge['label'] }}
                        </span>
                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                            {{ $att->clock_in_at ? $att->clock_in_at->format('H:i') . ' WIB' : '-' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-6 text-center text-slate-400 text-xs">Belum ada karyawan yang absen hari ini.</div>
                @endforelse
            </div>
        </div>

        <!-- Quick Opname Alert Card -->
        @if($lowStockProducts->isNotEmpty())
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-2">
            <div class="font-bold text-amber-900 flex items-center gap-1.5">
                <span>⚠️ Stok Menipis Membutuhkan Restock</span>
            </div>
            <div class="space-y-1 text-slate-700">
                @foreach($lowStockProducts as $lp)
                <div class="flex items-center justify-between text-[11px]">
                    <span class="truncate max-w-[180px]">{{ $lp->name }}</span>
                    <span class="font-mono font-bold text-amber-700 whitespace-nowrap">Sisa: {{ $lp->stock_qty }} {{ $lp->unit }}</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('warehouse.adjustments.index') }}" class="block text-center text-[11px] font-bold text-amber-800 hover:underline pt-1">
                Lakukan Restock Supplier di Gudang &rarr;
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
