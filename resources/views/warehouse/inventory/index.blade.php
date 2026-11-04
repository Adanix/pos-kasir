@extends('layouts.admin')

@section('title', 'Ringkasan Stok Inventori')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ringkasan Stok & Inventori</h1>
            <p class="text-sm text-slate-500">Monitoring level stok gudang, nilai aset barang dagangan, dan peringatan stok menipis.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warehouse.adjustments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Opname / Restock</span>
            </a>
            <a href="{{ route('warehouse.movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Kartu Stok</span>
            </a>
        </div>
    </div>

    <!-- Inventory KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk (SKU)</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalSkus }}</div>
                <span class="text-[11px] text-slate-500">Terdaftar di katalog</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Menipis (Alert)</span>
                <div class="text-2xl font-black text-amber-600 mt-1">{{ $lowStockCount }}</div>
                <span class="text-[11px] text-amber-600 font-medium">Di bawah batas aman</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Habis (Kosong)</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ $outOfStockCount }}</div>
                <span class="text-[11px] text-rose-600 font-medium">Perlu segera restock</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nilai Aset Modal</span>
                <div class="text-xl font-black text-emerald-600 mt-1">{{ format_rupiah($totalAssetValue) }}</div>
                <span class="text-[11px] text-slate-500">Estimasi omset {{ format_rupiah($totalSellingValue) }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('warehouse.inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Produk / SKU / Barcode</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau kode..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 focus:ring focus:ring-amber-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                <select name="category_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Stok</label>
                <select name="stock_status" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Kondisi --</option>
                    <option value="low" {{ $filterStock == 'low' ? 'selected' : '' }}>Stok Menipis (<= Batas Aman)</option>
                    <option value="out" {{ $filterStock == 'out' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Terapkan Filter
                </button>
                <a href="{{ route('warehouse.inventory.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Products Stock Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Nama Produk & Barcode</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Beli</th>
                        <th class="py-3 px-4">Harga Jual</th>
                        <th class="py-3 px-4 text-center">Stok Fisik</th>
                        <th class="py-3 px-4 text-center">Min. Stok</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $p->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    SKU: {{ $p->sku }} &bull; Barcode: {{ $p->barcode }}
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $p->category->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 whitespace-nowrap">
                                {{ format_rupiah($p->cost_price) }}
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                {{ format_rupiah($p->selling_price) }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="font-mono text-sm font-bold {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-800') }}">
                                    {{ $p->stock }} {{ $p->unit }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-500 whitespace-nowrap">
                                {{ $p->min_stock }} {{ $p->unit }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->stock <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Habis
                                    </span>
                                @elseif($p->stock <= $p->min_stock)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Menipis
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('warehouse.adjustments.index', ['product_id' => $p->id]) }}" class="px-2.5 py-1 text-amber-600 hover:text-amber-800 font-semibold hover:bg-amber-50 rounded-lg transition">
                                    Opname
                                </a>
                                <a href="{{ route('warehouse.movements.index', ['product_id' => $p->id]) }}" class="px-2.5 py-1 text-slate-600 hover:text-slate-800 font-semibold hover:bg-slate-100 rounded-lg transition">
                                    Kartu Stok
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Tidak ada produk yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
