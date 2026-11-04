@extends('layouts.store')

@section('title', 'Keranjang Belanja — OmniKasir Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Keranjang Belanja Anda</h1>
            <p class="text-xs sm:text-sm text-slate-500">Periksa kembali item barang yang ingin Anda beli sebelum checkout.</p>
        </div>
        <a href="{{ route('store.catalog') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
            &larr; Lanjut Belanja
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Items Table -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                                <th class="py-3 px-3">Produk</th>
                                <th class="py-3 px-3">Harga</th>
                                <th class="py-3 px-3 text-center">Jumlah</th>
                                <th class="py-3 px-3 text-right">Subtotal</th>
                                <th class="py-3 px-3 text-right">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($cartItems as $it)
                                <tr>
                                    <td class="py-4 px-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if($it['product']->image_url)
                                                    <img src="{{ $it['product']->image_url }}" alt="{{ $it['product']->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('store.product.show', $it['product']->slug) }}" class="font-bold text-slate-900 hover:text-emerald-600 line-clamp-1">
                                                    {{ $it['product']->name }}
                                                </a>
                                                <div class="text-[10px] text-slate-400 font-mono">
                                                    SKU: {{ $it['product']->sku }} &bull; {{ $it['product']->weight_grams }}g
                                                </div>
                                                @if(!$it['has_sufficient_stock'])
                                                    <span class="text-[10px] font-bold text-rose-600">Stok sisa {{ $it['product']->stock_qty }}!</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-3 font-mono font-medium text-slate-600 whitespace-nowrap">
                                        {{ format_rupiah($it['unit_price']) }}
                                    </td>
                                    <td class="py-4 px-3 text-center whitespace-nowrap">
                                        <form method="POST" action="{{ route('store.cart.update') }}" class="inline-flex items-center border border-slate-200 rounded-lg">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="product_id" value="{{ $it['product']->id }}">
                                            <button type="submit" name="qty" value="{{ max(1, $it['qty'] - 1) }}" class="px-2 py-1 text-slate-500 hover:bg-slate-100 font-bold">-</button>
                                            <span class="px-2 py-1 font-mono font-bold text-xs text-slate-800">{{ $it['qty'] }}</span>
                                            <button type="submit" name="qty" value="{{ min($it['product']->stock_qty, $it['qty'] + 1) }}" class="px-2 py-1 text-slate-500 hover:bg-slate-100 font-bold">+</button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                        {{ format_rupiah($it['line_total']) }}
                                    </td>
                                    <td class="py-4 px-3 text-right whitespace-nowrap">
                                        <form method="POST" action="{{ route('store.cart.remove') }}">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $it['product']->id }}">
                                            <button type="submit" class="text-slate-400 hover:text-rose-600 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary & Checkout Sidebar -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-6 h-fit">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Ringkasan Belanja</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Berat Paket:</span>
                        <span class="font-mono font-semibold">{{ number_format($totalWeightGrams, 0, ',', '.') }} gram</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Belanja:</span>
                        <span class="font-mono font-semibold">{{ format_rupiah($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Estimasi Ongkos Kirim:</span>
                        <span class="text-slate-400">Dihitung di checkout</span>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-slate-900">Total Sementara:</span>
                        <span class="text-xl font-black font-mono text-emerald-600">{{ format_rupiah($subtotal) }}</span>
                    </div>
                </div>

                <a href="{{ route('store.checkout.index') }}" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <span>Lanjut ke Pembayaran & Checkout &rarr;</span>
                </a>

                <div class="p-3 bg-slate-50 rounded-xl text-[11px] text-slate-500 space-y-1">
                    <div class="font-semibold text-slate-700">&check; Pilihan Ambil di Toko Kasir (Gratis)</div>
                    <div>&check; Tersedia opsi kurir JNE, J&T, SiCepat, Gosend</div>
                    <div>&check; Pembayaran QRIS instan atau Transfer VA</div>
                </div>
            </div>

        </div>
    @else
        <!-- Empty Cart -->
        <div class="py-20 text-center bg-white rounded-3xl border border-slate-200/80 p-8 space-y-4 max-w-lg mx-auto shadow-sm">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900">Keranjang Belanja Anda Kosong</h2>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Anda belum menambahkan produk apa pun ke dalam keranjang. Silakan jelajahi katalog online kami.
            </p>
            <a href="{{ route('store.catalog') }}" class="inline-block px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                Mulai Berbelanja Sekarang &rarr;
            </a>
        </div>
    @endif

</div>
@endsection
