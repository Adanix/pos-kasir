@extends('layouts.store')

@section('title', 'Pembayaran Pesanan #' . $order->order_number . ' — OmniKasir Store')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Header & Order Amount -->
    <div class="text-center space-y-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
            <span>Menunggu Pembayaran</span>
        </span>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Selesaikan Pembayaran Anda</h1>
        <p class="text-xs text-slate-500 font-mono">No. Pesanan: {{ $order->order_number }}</p>
        
        <div class="pt-2">
            <div class="text-xs text-slate-400">Total yang harus dibayar:</div>
            <div class="text-3xl font-black font-mono text-emerald-600">{{ format_rupiah($order->grand_total) }}</div>
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- PAYMENT INSTRUCTION CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        
        @if($order->payment_method === 'qris')
            <!-- QRIS SECTION -->
            <div class="text-center space-y-4">
                <div class="inline-block p-4 bg-white border-2 border-slate-900 rounded-3xl shadow-md">
                    <!-- QRIS Branding Header -->
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 mb-3 px-2">
                        <span class="text-xs font-black tracking-widest text-slate-900">QRIS</span>
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Standar Pembayaran Nasional</span>
                    </div>

                    <!-- Simulated QR Code SVG -->
                    <div class="w-56 h-56 mx-auto bg-slate-50 p-2 rounded-2xl flex flex-col items-center justify-center border border-slate-200">
                        <svg class="w-48 h-48" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Positioning markers -->
                            <rect x="5" y="5" width="26" height="26" fill="#0f172a" rx="4"/>
                            <rect x="9" y="9" width="18" height="18" fill="#fff" rx="2"/>
                            <rect x="13" y="13" width="10" height="10" fill="#0f172a"/>

                            <rect x="69" y="5" width="26" height="26" fill="#0f172a" rx="4"/>
                            <rect x="73" y="9" width="18" height="18" fill="#fff" rx="2"/>
                            <rect x="77" y="13" width="10" height="10" fill="#0f172a"/>

                            <rect x="5" y="69" width="26" height="26" fill="#0f172a" rx="4"/>
                            <rect x="9" y="73" width="18" height="18" fill="#fff" rx="2"/>
                            <rect x="13" y="77" width="10" height="10" fill="#0f172a"/>

                            <!-- Grid pattern simulation -->
                            <rect x="36" y="8" width="6" height="6" fill="#0f172a"/>
                            <rect x="46" y="8" width="6" height="6" fill="#0f172a"/>
                            <rect x="56" y="8" width="6" height="6" fill="#0f172a"/>
                            <rect x="36" y="18" width="6" height="6" fill="#0f172a"/>
                            <rect x="56" y="18" width="6" height="6" fill="#0f172a"/>
                            <rect x="36" y="28" width="26" height="6" fill="#0f172a"/>

                            <!-- Middle area -->
                            <rect x="8" y="36" width="6" height="26" fill="#0f172a"/>
                            <rect x="22" y="36" width="12" height="6" fill="#0f172a"/>
                            <rect x="38" y="38" width="24" height="24" fill="#059669" rx="4"/>
                            <text x="50" y="54" fill="#fff" font-size="10" font-weight="bold" text-anchor="middle">OK</text>

                            <rect x="68" y="36" width="24" height="6" fill="#0f172a"/>
                            <rect x="68" y="48" width="10" height="14" fill="#0f172a"/>
                            <rect x="84" y="48" width="8" height="8" fill="#0f172a"/>

                            <!-- Bottom patterns -->
                            <rect x="36" y="68" width="12" height="6" fill="#0f172a"/>
                            <rect x="54" y="68" width="24" height="6" fill="#0f172a"/>
                            <rect x="36" y="78" width="6" height="14" fill="#0f172a"/>
                            <rect x="48" y="78" width="16" height="6" fill="#0f172a"/>
                            <rect x="70" y="78" width="22" height="14" fill="#0f172a"/>
                        </svg>
                    </div>

                    <div class="mt-3 text-center">
                        <div class="text-xs font-bold text-slate-900">OMNIKASIR NUSANTARA</div>
                        <div class="text-[10px] text-slate-400 font-mono">NMID: ID1020039482910</div>
                    </div>
                </div>

                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    Buka aplikasi BCA Mobile, GoPay, OVO, Dana, atau ShopeePay di ponsel Anda, pilih menu Scan / Bayar, dan arahkan kamera ke kode QR di atas.
                </p>
            </div>

        @elseif($order->payment_method === 'bank_transfer')
            <!-- VIRTUAL ACCOUNT SECTION -->
            <div class="space-y-4 max-w-md mx-auto">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center space-y-2">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Nomor Virtual Account {{ $vaPayload['bank'] ?? 'BCA' }}</span>
                    <div class="flex items-center justify-center gap-2">
                        <span class="text-2xl font-black font-mono text-slate-900 tracking-wider">
                            {{ $vaPayload['va_number'] ?? '8801200000001' }}
                        </span>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $vaPayload['va_number'] ?? '8801200000001' }}'); alert('Nomor Virtual Account berhasil disalin!')" class="p-1.5 text-slate-500 hover:text-slate-800 bg-white rounded-lg border border-slate-200 transition" title="Salin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        </button>
                    </div>
                    <div class="text-[11px] text-slate-500">Atas Nama: <strong class="text-slate-700">OMNIKASIR - {{ $order->customer_name }}</strong></div>
                </div>

                <div class="space-y-2 text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="font-bold text-slate-900">Petunjuk Pembayaran ATM / Mobile Banking:</div>
                    <ol class="list-decimal pl-4 space-y-1">
                        <li>Pilih menu <strong>Transfer &gt; Virtual Account</strong>.</li>
                        <li>Masukkan nomor VA di atas.</li>
                        <li>Pastikan jumlah tagihan sesuai <strong>{{ format_rupiah($order->grand_total) }}</strong>.</li>
                        <li>Konfirmasi transaksi. Sistem akan memverifikasi secara otomatis tanpa unggah struk.</li>
                    </ol>
                </div>
            </div>

        @else
            <!-- COD SECTION -->
            <div class="text-center py-6 space-y-3 max-w-md mx-auto">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900">Pembayaran COD (Bayar Tunai di Tempat)</h2>
                <p class="text-xs text-slate-500">
                    Siapkan uang pas sebesar <strong>{{ format_rupiah($order->grand_total) }}</strong> saat kurir mengantarkan pesanan atau saat Anda mengambil barang di meja kasir.
                </p>
            </div>
        @endif

    </div>

    <!-- PAYMENT SIMULATOR CONTROLS (DEMO SANDBOX) -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-6 sm:p-8 space-y-4 shadow-xl border border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Simulator Gateway Pembayaran (Mode Demo)</h3>
                    <p class="text-[11px] text-slate-400">Uji coba simulasi callback webhook gateway pembayaran secara langsung tanpa kartu debit/e-wallet asli.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/40 rounded-full text-[10px] font-mono font-bold uppercase">
                Sandbox Mode
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
            
            <!-- Success Simulator Button -->
            <form method="POST" action="{{ route('store.payment.simulate', $order->order_number) }}">
                @csrf
                <input type="hidden" name="outcome" value="success">
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-950 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simulasikan Berhasil Lunas</span>
                </button>
            </form>

            <!-- Failed Simulator Button -->
            <form method="POST" action="{{ route('store.payment.simulate', $order->order_number) }}">
                @csrf
                <input type="hidden" name="outcome" value="failed">
                <button type="submit" class="w-full py-3 bg-rose-600/80 hover:bg-rose-600 text-white font-semibold text-xs rounded-xl border border-rose-500 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Simulasikan Gagal Bayar</span>
                </button>
            </form>

            <!-- Cancelled Simulator Button -->
            <form method="POST" action="{{ route('store.payment.simulate', $order->order_number) }}">
                @csrf
                <input type="hidden" name="outcome" value="cancelled">
                <button type="submit" onclick="return confirm('Batalkan pesanan ini dan kembalikan stok barang ke toko?')" class="w-full py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Batalkan Pesanan (Release Stok)</span>
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
