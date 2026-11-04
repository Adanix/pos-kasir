@extends('layouts.store')

@section('title', $product->name . ' — OmniKasir Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('store.catalog') }}" class="hover:text-emerald-600">Katalog Online</a>
        <span>&rsaquo;</span>
        @if($product->category)
            <a href="{{ route('store.catalog', ['category' => $product->category->slug]) }}" class="hover:text-emerald-600">{{ $product->category->name }}</a>
            <span>&rsaquo;</span>
        @endif
        <span class="text-slate-800 font-medium truncate">{{ $product->name }}</span>
    </nav>

    <!-- Product Details Section -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 sm:p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12">
            
            <!-- Product Media -->
            <div class="space-y-4">
                <div class="aspect-square bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-200/80">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-center text-slate-400 p-6">
                            <svg class="w-20 h-20 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs">Foto Produk Pratinjau</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Purchase Action & Info -->
            <div class="flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 uppercase tracking-wider">
                            {{ $product->category->name ?? 'Kategori' }}
                        </span>
                        <span class="text-xs font-mono text-slate-400">SKU: {{ $product->sku }}</span>
                        @if($product->barcode)
                            <span class="text-xs font-mono text-slate-400">&bull; Barcode: {{ $product->barcode }}</span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Harga Satuan</span>
                            <span class="text-3xl font-black font-mono text-emerald-600">
                                {{ format_rupiah($product->selling_price) }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Ketersediaan Stok</span>
                            <span class="text-xs font-bold font-mono {{ $product->stock_qty <= 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                {{ $product->stock_qty > 0 ? $product->stock_qty . ' ' . $product->unit . ' Tersedia' : 'Stok Habis' }}
                            </span>
                        </div>
                    </div>

                    <!-- Meta specs -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Berat Pengiriman:</span>
                            <span class="font-bold font-mono text-slate-800">{{ $product->weight_grams }} gram</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px]">Satuan Penjualan:</span>
                            <span class="font-bold text-slate-800">{{ $product->unit }}</span>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    @if($product->stock_qty > 0)
                        <form method="POST" action="{{ route('store.cart.add') }}" class="space-y-4 pt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-slate-700">Jumlah Beli:</span>
                                <div class="flex items-center border border-slate-300 rounded-xl bg-white overflow-hidden" x-data="{ qty: 1, maxStock: {{ $product->stock_qty }} }">
                                    <button type="button" @click="if(qty > 1) qty--" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-bold text-sm">-</button>
                                    <input type="number" name="qty" x-model.number="qty" min="1" :max="maxStock" class="w-14 text-center border-0 py-2 text-xs font-mono font-bold text-slate-900 focus:ring-0">
                                    <button type="button" @click="if(qty < maxStock) qty++" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-bold text-sm">+</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                                
                                <button type="submit" name="buy_now" value="1" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition">
                                    Beli Sekarang &rarr;
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700">
                            <strong>Stok Produk Habis:</strong> Produk ini saat ini sedang tidak dapat dipesan secara online karena stok di gudang dan kasir telah habis.
                        </div>
                    @endif
                </div>

                <!-- Guarantee Badges -->
                <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-[10px] text-slate-500">
                    <div class="p-2">
                        <svg class="w-5 h-5 mx-auto mb-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Stok Asli Gudang</span>
                    </div>
                    <div class="p-2">
                        <svg class="w-5 h-5 mx-auto mb-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Bisa Ambil di Kasir</span>
                    </div>
                    <div class="p-2">
                        <svg class="w-5 h-5 mx-auto mb-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>QRIS / VA / COD</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Description Tab -->
        <div class="mt-8 pt-8 border-t border-slate-200">
            <h2 class="text-base font-bold text-slate-900 mb-3">Deskripsi Produk Lengkap</h2>
            <div class="prose prose-sm max-w-none text-slate-600 text-xs leading-relaxed">
                {!! nl2br(e($product->description ?: 'Tidak ada deskripsi detail untuk produk ini.')) !!}
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Produk Terkait Lainnya</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($relatedProducts as $rel)
                    <a href="{{ route('store.product.show', $rel->slug) }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 hover:shadow-md hover:border-emerald-500/50 transition flex flex-col justify-between group">
                        <div>
                            <div class="aspect-square bg-slate-50 rounded-xl overflow-hidden mb-3">
                                @if($rel->image_url)
                                    <img src="{{ $rel->image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                @endif
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 group-hover:text-emerald-600 line-clamp-2">{{ $rel->name }}</h4>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 font-mono font-bold text-emerald-600 text-xs">
                            {{ format_rupiah($rel->selling_price) }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
