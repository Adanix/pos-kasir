@extends('layouts.admin')

@section('title', 'Katalog Produk & Kategori')

@section('content')
<div class="space-y-6" x-data="{ catModal: false }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Katalog Produk & Kategori</h1>
            <p class="text-sm text-slate-500">Master data SKU produk, barcode scanner, harga modal/jual, dan status listing online.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="catModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                <span>Kelola Kategori</span>
            </button>
            <a href="{{ route('warehouse.products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Produk SKU Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('warehouse.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Produk / SKU / Barcode</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kata kunci..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
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
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Cari
                </button>
                <a href="{{ route('warehouse.products.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Nama Produk & Barcode</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Harga Beli / Jual</th>
                        <th class="py-3 px-4 text-center">Stok</th>
                        <th class="py-3 px-4 text-center">Listing Online</th>
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
                                    SKU: <span class="text-slate-600 font-semibold">{{ $p->sku }}</span> &bull; 
                                    Barcode: <span class="text-slate-600 font-semibold">{{ $p->barcode }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $p->category->name ?? '-' }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-bold text-emerald-600 font-mono">{{ format_rupiah($p->selling_price) }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">Beli: {{ format_rupiah($p->cost_price) }}</div>
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-semibold text-slate-800 whitespace-nowrap">
                                {{ $p->stock }} {{ $p->unit }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->is_online_listed)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Toko Online Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                        Hanya POS Kasir
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('warehouse.products.edit', $p) }}" class="px-2.5 py-1 text-amber-600 hover:text-amber-800 font-semibold hover:bg-amber-50 rounded-lg transition">
                                    Edit
                                </a>
                                <a href="{{ route('warehouse.movements.index', ['product_id' => $p->id]) }}" class="px-2.5 py-1 text-slate-600 hover:text-slate-800 font-semibold hover:bg-slate-100 rounded-lg transition">
                                    Kartu Stok
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada produk yang terdaftar.</td>
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

    <!-- Category Modal -->
    <div x-show="catModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="catModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Kelola Kategori Produk</h3>
                <button @click="catModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Create Category Form -->
            <form method="POST" action="{{ route('warehouse.categories.store') }}" class="p-3 bg-slate-50 rounded-xl space-y-2">
                @csrf
                <div class="text-xs font-bold text-slate-700">Tambah Kategori Baru</div>
                <div class="flex gap-2">
                    <input type="text" name="name" placeholder="Nama Kategori (cth: Perlengkapan Kasir)" required class="flex-1 text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Tambah
                    </button>
                </div>
            </form>

            <!-- List Categories -->
            <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 text-xs">
                @foreach($categories as $cat)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-800">{{ $cat->name }}</span>
                            <span class="text-slate-400 text-[10px] ml-1 font-mono">({{ $cat->products()->count() }} produk)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" @click="catModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection
