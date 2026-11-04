@extends('layouts.store')

@section('title', 'Katalog Produk Toko Online')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Hero Announcement Card -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 text-white p-6 sm:p-10 shadow-xl border border-emerald-800/30">
        <div class="relative z-10 max-w-2xl space-y-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sinkronisasi Stok Real-Time dengan Kasir POS</span>
            </span>
            <h1 class="text-2xl sm:text-4xl font-black tracking-tight text-white leading-tight">
                Peralatan Kasir, POS & Retail Nusantara
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Pesan online dengan opsi pengiriman kurir ekspedisi ke seluruh Indonesia atau ambil langsung di kasir toko fisik (BOPIS).
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Filters & Sort -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-200">
        
        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs">
            <a href="{{ route('store.catalog', array_merge(request()->except('category', 'page'))) }}" 
               class="px-3.5 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ empty($categorySlug) ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('store.catalog', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                   class="px-3.5 py-1.5 rounded-xl font-semibold transition whitespace-nowrap {{ $categorySlug === $cat->slug ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                    {{ $cat->name }} ({{ $cat->products_count }})
                </a>
            @endforeach
        </div>

        <!-- Sort Select -->
        <div class="flex items-center gap-2 self-end text-xs">
            <span class="text-slate-500 font-medium">Urutkan:</span>
            <form method="GET" action="{{ route('store.catalog') }}" class="inline-block">
                @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                @if($categorySlug) <input type="hidden" name="category" value="{{ $categorySlug }}"> @endif
                <select name="sort" onchange="this.form.submit()" class="py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:border-emerald-500">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="price_low" {{ $sort === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_high" {{ $sort === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Nama Produk A-Z</option>
                </select>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <a href="{{ route('store.cart.index') }}" class="underline font-bold">Lihat Keranjang &rarr;</a>
        </div>
    @endif

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($products as $prod)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition flex flex-col overflow-hidden group">
                
                <!-- Image Container -->
                <a href="{{ route('store.product.show', $prod->slug) }}" class="relative block aspect-square bg-slate-100 overflow-hidden">
                    @if($prod->image_url)
                        <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300 group-hover:scale-105 transition duration-300">
                            <svg class="w-16 h-16 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif

                    <!-- Stock Status Badge -->
                    <div class="absolute top-3 left-3">
                        @if($prod->stock_qty <= 0)
                            <span class="px-2.5 py-1 rounded-full bg-rose-600 text-white font-bold text-[10px] shadow">
                                Stok Habis
                            </span>
                        @elseif($prod->stock_qty <= 5)
                            <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white font-bold text-[10px] shadow">
                                Sisa {{ $prod->stock_qty }} {{ $prod->unit }}
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-600/90 text-white font-semibold text-[10px] backdrop-blur-sm shadow">
                                Stok Tersedia
                            </span>
                        @endif
                    </div>
                </a>

                <!-- Product Content -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            {{ $prod->category->name ?? 'Kategori' }}
                        </div>
                        <a href="{{ route('store.product.show', $prod->slug) }}" class="text-sm font-bold text-slate-900 hover:text-emerald-600 line-clamp-2 transition leading-snug">
                            {{ $prod->name }}
                        </a>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Harga</span>
                            <span class="text-base font-black font-mono text-emerald-600 leading-none">
                                {{ format_rupiah($prod->selling_price) }}
                            </span>
                        </div>

                        <!-- Add to Cart Form -->
                        @if($prod->stock_qty > 0)
                            <form method="POST" action="{{ route('store.cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $prod->id }}">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="w-9 h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-sm transition" title="Tambah ke Keranjang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-20 text-center text-slate-400">
                <svg class="w-16 h-16 mx-auto mb-3 opacity-30 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <h3 class="text-base font-bold text-slate-700">Tidak ada produk ditemukan</h3>
                <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian yang berbeda atau hapus filter kategori.</p>
                <a href="{{ route('store.catalog') }}" class="mt-4 inline-block px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl">Lihat Semua Produk</a>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="pt-6">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
