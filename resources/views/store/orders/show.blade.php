@extends('layouts.store')

@section('title', 'Pesanan #' . $order->order_number . ' — OmniKasir Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Actions & Status Alert -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight font-mono">{{ $order->order_number }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : ($order->status === 'shipped' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800')) }}">
                    {{ str_replace('_', ' ', $order->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Dipesan pada {{ format_tanggal($order->ordered_at, 'd M Y H:i:s') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak E-Receipt</span>
            </button>

            @if(!$order->isPaid() && $order->status !== 'cancelled')
                <a href="{{ route('store.payment.show', $order->order_number) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                    Selesaikan Pembayaran &rarr;
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 bg-sky-50 border border-sky-200 text-sky-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- TIMELINE TRACKING PROGRESS -->
    @if($order->status !== 'cancelled')
        @php
            $isPaid = $order->isPaid();
            $isProcessing = in_array($order->status, ['processing', 'shipped', 'delivered', 'completed']);
            $isShipped = in_array($order->status, ['shipped', 'delivered', 'completed']);
            $isCompleted = ($order->status === 'completed');
        @endphp

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6">Status Progres Pesanan</h2>
            
            <div class="relative flex flex-col md:flex-row justify-between gap-6 md:gap-2">
                <!-- Step 1 -->
                <div class="flex md:flex-col items-center md:items-center text-center gap-3 md:gap-2 flex-1">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow">
                        &check;
                    </div>
                    <div class="text-left md:text-center">
                        <div class="text-xs font-bold text-slate-900">Pesanan Dibuat</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ format_tanggal($order->ordered_at, 'H:i') }}</div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex md:flex-col items-center md:items-center text-center gap-3 md:gap-2 flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isPaid ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-400' }}">
                        {{ $isPaid ? '✓' : '2' }}
                    </div>
                    <div class="text-left md:text-center">
                        <div class="text-xs font-bold {{ $isPaid ? 'text-slate-900' : 'text-slate-400' }}">Pembayaran Terverifikasi</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $isPaid ? 'Lunas' : 'Menunggu' }}</div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex md:flex-col items-center md:items-center text-center gap-3 md:gap-2 flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isProcessing ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-400' }}">
                        {{ $isProcessing ? '✓' : '3' }}
                    </div>
                    <div class="text-left md:text-center">
                        <div class="text-xs font-bold {{ $isProcessing ? 'text-slate-900' : 'text-slate-400' }}">Packing & Penyiapan</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $isProcessing ? 'Gudang/Kasir' : 'Antrean' }}</div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex md:flex-col items-center md:items-center text-center gap-3 md:gap-2 flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isShipped ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-400' }}">
                        {{ $isShipped ? '✓' : '4' }}
                    </div>
                    <div class="text-left md:text-center">
                        <div class="text-xs font-bold {{ $isShipped ? 'text-slate-900' : 'text-slate-400' }}">
                            {{ $order->fulfillment_type === 'pickup' ? 'Siap Diambil di Kasir' : 'Dalam Pengiriman' }}
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $order->shipping_tracking_number ?: 'Ekspedisi' }}</div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex md:flex-col items-center md:items-center text-center gap-3 md:gap-2 flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isCompleted ? 'bg-emerald-600 text-white shadow' : 'bg-slate-100 text-slate-400' }}">
                        {{ $isCompleted ? '✓' : '5' }}
                    </div>
                    <div class="text-left md:text-center">
                        <div class="text-xs font-bold {{ $isCompleted ? 'text-slate-900' : 'text-slate-400' }}">Pesanan Selesai</div>
                        <div class="text-[10px] text-slate-400 font-mono">{{ $isCompleted ? 'Selesai' : 'Pending' }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- INVOICE DETAILS & ITEMS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Items & Shipping -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Items Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Daftar Item Pesanan</h3>
                <div class="divide-y divide-slate-100">
                    @foreach($order->items as $it)
                        <div class="py-3 flex items-center justify-between gap-4 text-xs">
                            <div class="flex-1">
                                <div class="font-bold text-slate-900">{{ $it->product_name_snapshot }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">
                                    {{ $it->qty }}x @ {{ format_rupiah($it->unit_price) }}
                                </div>
                            </div>
                            <div class="font-mono font-bold text-slate-900">
                                {{ format_rupiah($it->line_total) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Info Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-3 text-xs">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Penerima & Pengiriman</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-600">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama Penerima:</span>
                        <strong class="text-slate-900">{{ $order->customer_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">No. Telepon / WA:</span>
                        <span class="font-mono">{{ $order->customer_phone }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block text-[10px]">Metode Pengambilan / Kurir:</span>
                        <strong class="text-slate-900">{{ $order->fulfillment_type === 'pickup' ? 'Ambil di Toko Fisik (BOPIS)' : ($order->shipping_service ?? $order->shipping_courier) }}</strong>
                        @if($order->shipping_tracking_number)
                            <div class="mt-1 text-slate-700">
                                Nomor Resi Kurir: <span class="font-mono font-bold text-sky-600">{{ $order->shipping_tracking_number }}</span>
                            </div>
                        @endif
                    </div>
                    @if($order->customer_address)
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 block text-[10px]">Alamat Pengiriman:</span>
                            <span>{{ $order->customer_address }}</span>
                        </div>
                    @endif
                    @if($order->customer_notes)
                        <div class="sm:col-span-2 p-2.5 bg-amber-50 rounded-xl text-amber-800 text-[11px]">
                            Catatan: "{{ $order->customer_notes }}"
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Col: Financial Summary -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Rincian Pembayaran</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Barang:</span>
                        <span class="font-mono font-semibold">{{ format_rupiah($order->subtotal) }}</span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim:</span>
                        <span class="font-mono font-semibold">{{ format_rupiah($order->shipping_cost) }}</span>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex justify-between items-baseline">
                        <span class="font-bold text-slate-900">Total Tagihan:</span>
                        <span class="text-xl font-black font-mono text-emerald-600">{{ format_rupiah($order->grand_total) }}</span>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Metode Pembayaran</div>
                    <div class="font-bold text-slate-800 uppercase font-mono">{{ $order->payment_method }}</div>
                    <div class="text-[11px] font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $order->payment_status === 'paid' ? 'Sudah Lunas' : 'Belum Dibayar' }}
                    </div>
                </div>

                <a href="{{ route('store.catalog') }}" class="block w-full py-2.5 text-center text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-xl border border-slate-200 transition">
                    &larr; Kembali ke Katalog Toko
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
