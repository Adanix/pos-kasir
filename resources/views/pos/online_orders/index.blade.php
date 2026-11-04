@extends('layouts.pos')

@section('title', 'Antrean Pesanan Toko Online')

@section('content')
<div class="flex-1 overflow-y-auto p-4 sm:p-6 max-w-6xl mx-auto w-full space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Antrean Pesanan Toko Online</h1>
            <p class="text-xs sm:text-sm text-slate-400">Sinkronisasi pesanan masuk dari toko online publik ke kasir fisik (BOPIS & Delivery).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('store.catalog') }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 flex items-center gap-1.5 transition">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                <span>Buka Toko Online</span>
            </a>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl transition">
                &larr; Buka Kasir POS
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-950/60 border border-emerald-500/50 rounded-2xl text-xs text-emerald-200 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-950/60 border border-rose-500/50 rounded-2xl text-xs text-rose-200 flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Status Tabs / Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('pos.online-orders.index') }}" class="p-4 rounded-2xl border transition {{ empty($status) ? 'bg-slate-800 border-emerald-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:bg-slate-800/60' }}">
            <span class="text-[10px] uppercase font-bold tracking-wider">Semua Pesanan</span>
            <div class="text-xl font-black font-mono text-white mt-1">{{ $orders->total() }}</div>
        </a>

        <a href="{{ route('pos.online-orders.index', ['status' => 'pending_payment']) }}" class="p-4 rounded-2xl border transition {{ $status === 'pending_payment' ? 'bg-slate-800 border-amber-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:bg-slate-800/60' }}">
            <span class="text-[10px] uppercase font-bold tracking-wider text-amber-400">Menunggu Pembayaran</span>
            <div class="text-xl font-black font-mono text-amber-400 mt-1">{{ $counts['pending_payment'] }}</div>
        </a>

        <a href="{{ route('pos.online-orders.index', ['status' => 'processing']) }}" class="p-4 rounded-2xl border transition {{ $status === 'processing' ? 'bg-slate-800 border-sky-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:bg-slate-800/60' }}">
            <span class="text-[10px] uppercase font-bold tracking-wider text-sky-400">Sedang Diproses/Packing</span>
            <div class="text-xl font-black font-mono text-sky-400 mt-1">{{ $counts['paid_or_processing'] }}</div>
        </a>

        <a href="{{ route('pos.online-orders.index', ['status' => 'shipped']) }}" class="p-4 rounded-2xl border transition {{ $status === 'shipped' ? 'bg-slate-800 border-emerald-500 text-white' : 'bg-slate-900 border-slate-800 text-slate-400 hover:bg-slate-800/60' }}">
            <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400">Siap Ambil / Dikirim</span>
            <div class="text-xl font-black font-mono text-emerald-400 mt-1">{{ $counts['shipped'] }}</div>
        </a>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($orders as $ord)
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 space-y-4 hover:border-slate-700 transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs">
                            {{ $ord->fulfillment_type === 'pickup' ? 'BOPIS' : 'KIRIM' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-white font-mono">{{ $ord->order_number }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $ord->status === 'completed' ? 'bg-emerald-950 text-emerald-300 border border-emerald-800' : ($ord->status === 'shipped' ? 'bg-sky-950 text-sky-300 border border-sky-800' : ($ord->status === 'processing' ? 'bg-indigo-950 text-indigo-300 border border-indigo-800' : 'bg-amber-950 text-amber-300 border border-amber-800')) }}">
                                    {{ str_replace('_', ' ', $ord->status) }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ format_tanggal($ord->ordered_at, 'd M Y H:i') }} &bull; Pelanggan: <strong class="text-slate-200">{{ $ord->customer_name }}</strong> ({{ $ord->customer_phone }})
                            </p>
                        </div>
                    </div>

                    <!-- Payment Badge & Total -->
                    <div class="text-right">
                        <div class="text-base font-black font-mono text-emerald-400">{{ format_rupiah($ord->grand_total) }}</div>
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            Metode: {{ strtoupper($ord->payment_method) }} &bull; 
                            <span class="{{ $ord->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $ord->payment_status === 'paid' ? 'LUNAS' : 'BELUM DIBAYAR' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Items & Shipping Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800/80 space-y-1.5">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Item Dipesan:</div>
                        <ul class="space-y-1 divide-y divide-slate-800/60">
                            @foreach($ord->items as $it)
                                <li class="pt-1 flex justify-between text-slate-300">
                                    <span>{{ $it->qty }}x {{ $it->product_name_snapshot }}</span>
                                    <span class="font-mono text-slate-400">{{ format_rupiah($it->line_total) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800/80 space-y-1 text-slate-300">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Fulfillment & Pengiriman:</div>
                        <div>Tipe: <strong class="text-white">{{ $ord->fulfillment_type === 'pickup' ? 'Ambil di Toko Kasir' : 'Pengiriman Ekspedisi' }}</strong></div>
                        @if($ord->fulfillment_type === 'delivery')
                            <div>Kurir: {{ $ord->shipping_service ?? $ord->shipping_courier }} (Ongkir: {{ format_rupiah($ord->shipping_cost) }})</div>
                            @if($ord->shipping_tracking_number)
                                <div>No. Resi: <span class="font-mono font-bold text-sky-400">{{ $ord->shipping_tracking_number }}</span></div>
                            @endif
                            <div class="text-slate-400 text-[11px] truncate">Alamat: {{ $ord->customer_address ?: '—' }}</div>
                        @endif
                        @if($ord->customer_notes)
                            <div class="text-amber-300 text-[11px] italic">Catatan: "{{ $ord->customer_notes }}"</div>
                        @endif
                    </div>
                </div>

                <!-- Actions for Cashier -->
                <div class="flex flex-wrap items-center justify-end gap-2 pt-2 border-t border-slate-800">
                    
                    @if($ord->payment_status !== 'paid')
                        <form method="POST" action="{{ route('pos.online-orders.confirm-payment', $ord) }}">
                            @csrf
                            <button type="submit" onclick="return confirm('Konfirmasi bahwa pembayaran pelanggan telah diterima?')" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition">
                                Konfirmasi Pembayaran Lunas
                            </button>
                        </form>
                    @endif

                    <!-- Change Status Buttons -->
                    @if($ord->status === 'processing')
                        <form method="POST" action="{{ route('pos.online-orders.status', $ord) }}">
                            @csrf
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="px-3.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl transition">
                                {{ $ord->fulfillment_type === 'pickup' ? 'Siap Diambil Pelanggan' : 'Kirim / Serahkan ke Kurir' }}
                            </button>
                        </form>
                    @elseif($ord->status === 'shipped')
                        <form method="POST" action="{{ route('pos.online-orders.status', $ord) }}">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl transition">
                                Selesaikan Pesanan (Selesai)
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('pos.orders.receipt', ['order' => $ord, 'width' => '58']) }}" target="_blank" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                        Cetak Struk Thermal
                    </a>

                </div>
            </div>
        @empty
            <div class="py-16 text-center text-slate-500 bg-slate-900 border border-slate-800 rounded-3xl">
                <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <p class="text-sm font-semibold">Tidak ada pesanan online pada kategori ini.</p>
                <p class="text-xs text-slate-500 mt-1">Pesanan yang dibuat oleh pelanggan di toko online akan otomatis muncul di sini.</p>
            </div>
        @endforelse
    </div>

    @if($orders->hasPages())
        <div class="p-4 bg-slate-900 border border-slate-800 rounded-2xl">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
