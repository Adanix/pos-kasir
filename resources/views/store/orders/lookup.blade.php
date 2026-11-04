@extends('layouts.store')

@section('title', 'Lacak Pesanan — OmniKasir Store')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header & Search Box -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Lacak Status Pesanan & E-Receipt</h1>
            <p class="text-xs sm:text-sm text-slate-500">Masukkan Nomor Pesanan (ORD-WEB-...) atau Nomor WhatsApp yang digunakan saat memesan.</p>
        </div>

        <form method="GET" action="{{ route('store.orders.lookup') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       name="q" 
                       value="{{ $query }}" 
                       required 
                       placeholder="Contoh: ORD-WEB-20261104-ABCDE atau 08123456789" 
                       class="w-full pl-9 pr-4 py-3 bg-slate-50 border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
            </div>
            <button type="submit" class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow transition">
                Lacak Sekarang
            </button>
        </form>
    </div>

    <!-- Search Results -->
    @if($query)
        <div class="space-y-4">
            <h2 class="text-sm font-bold text-slate-700">Hasil Pencarian untuk "{{ $query }}":</h2>

            @forelse($orders as $ord)
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:border-emerald-500/60 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold font-mono text-sm text-slate-900">{{ $ord->order_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $ord->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($ord->status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ str_replace('_', ' ', $ord->status) }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500">
                            {{ format_tanggal($ord->ordered_at, 'd M Y H:i') }} &bull; Penerima: <strong class="text-slate-700">{{ $ord->customer_name }}</strong>
                        </div>
                        <div class="text-[11px] text-slate-400">
                            {{ $ord->items->count() }} item barang &bull; {{ $ord->fulfillment_type === 'pickup' ? 'Ambil di Toko' : 'Kirim Ekspedisi' }}
                        </div>
                    </div>

                    <div class="flex items-center gap-4 self-end sm:self-center">
                        <div class="text-right">
                            <div class="text-base font-black font-mono text-emerald-600">{{ format_rupiah($ord->grand_total) }}</div>
                            <span class="text-[10px] uppercase font-semibold text-slate-400">{{ $ord->payment_method }}</span>
                        </div>
                        <a href="{{ route('store.orders.show', $ord->order_number) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                            Lihat Detail & E-Receipt &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center bg-white rounded-3xl border border-slate-200/80 p-6 space-y-2 text-slate-500">
                    <svg class="w-12 h-12 mx-auto opacity-30 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-semibold">Pesanan tidak ditemukan.</p>
                    <p class="text-xs text-slate-400">Pastikan nomor pesanan atau nomor WhatsApp yang dimasukkan sudah sesuai.</p>
                </div>
            @endforelse
        </div>
    @endif

</div>
@endsection
