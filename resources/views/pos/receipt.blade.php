<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Struk Thermal #{{ $order->order_number }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: #f1f5f9;
            color: #000;
        }

        .receipt-container {
            width: {{ $paperWidth === '80' ? '80mm' : '58mm' }};
            max-width: 100%;
            margin: 20px auto;
            background: #fff;
            padding: {{ $paperWidth === '80' ? '14px' : '8px' }};
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            font-size: {{ $paperWidth === '80' ? '11px' : '9.5px' }};
            line-height: 1.35;
        }

        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body class="p-2 sm:p-6" x-data="receiptPage()">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print max-w-md mx-auto mb-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-200 space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-sm font-bold text-slate-900">Pratinjau Struk Thermal</h1>
                <p class="text-xs text-slate-500">Pilih lebar kertas thermal kasir</p>
            </div>
            <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                <a href="{{ route('pos.orders.receipt', ['order' => $order, 'width' => '58']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ $paperWidth === '58' ? 'bg-white shadow text-slate-900' : 'text-slate-600' }}">58mm</a>
                <a href="{{ route('pos.orders.receipt', ['order' => $order, 'width' => '80']) }}" class="px-3 py-1 rounded-lg text-xs font-bold transition {{ $paperWidth === '80' ? 'bg-white shadow text-slate-900' : 'text-slate-600' }}">80mm</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="window.print()" class="py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Browser (Ctrl+P)</span>
            </button>

            <button type="button" @click="testEscPosBridge()" class="py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <span>Simulasi Driver ESC/POS</span>
            </button>
        </div>

        <!-- ESC/POS Output Modal / Drawer -->
        <div x-show="escPosResult" x-cloak class="mt-3 p-3 bg-slate-950 text-slate-300 rounded-xl text-xs font-mono space-y-2">
            <div class="flex justify-between items-center text-[10px] text-slate-400 border-b border-slate-800 pb-1">
                <span>SIMULASI HARDWARE ESC/POS BRIDGE</span>
                <button @click="escPosResult = null" class="text-rose-400 hover:text-rose-300">Tutup</button>
            </div>
            <div class="text-emerald-400 font-bold" x-text="escPosResult ? escPosResult.message : ''"></div>
            <div class="max-h-40 overflow-y-auto text-[10px] text-slate-400 bg-slate-900 p-2 rounded">
                <template x-if="escPosResult && escPosResult.raw_stream">
                    <pre x-text="escPosResult.raw_stream.join('\n')"></pre>
                </template>
            </div>
        </div>
    </div>

    <!-- THERMAL RECEIPT SLIP -->
    <div class="receipt-container">
        
        <!-- Header -->
        <div class="text-center pb-2 border-b border-dashed border-black">
            <div class="font-bold text-base tracking-wider uppercase">OMNIKASIR NUSANTARA</div>
            <div class="text-[10px]">Jl. Pemuda No. 45, Semarang</div>
            <div class="text-[10px]">Telp/WA: 0812-8899-7766</div>
        </div>

        <!-- Meta Info -->
        <div class="py-2 border-b border-dashed border-black space-y-0.5 text-[10px]">
            <div class="flex justify-between">
                <span>No. Transaksi</span>
                <span class="font-bold">{{ $order->order_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal</span>
                <span>{{ format_tanggal($order->ordered_at, 'd/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir</span>
                <span>{{ $order->cashier->name ?? 'Kasir Utama' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pelanggan</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            @if($order->channel === 'online')
                <div class="flex justify-between font-bold">
                    <span>Saluran</span>
                    <span>ONLINE ({{ strtoupper($order->fulfillment_type) }})</span>
                </div>
            @endif
        </div>

        <!-- Items Table -->
        <div class="py-2 border-b border-dashed border-black space-y-1.5">
            @foreach($order->items as $it)
                <div>
                    <div class="font-bold truncate">{{ $it->product_name_snapshot }}</div>
                    <div class="flex justify-between text-[10px]">
                        <span>{{ $it->qty }}x {{ format_rupiah($it->unit_price, false) }}</span>
                        <span>{{ format_rupiah($it->line_total, false) }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totals Breakdown -->
        <div class="py-2 border-b border-dashed border-black space-y-0.5 text-[10px]">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>{{ format_rupiah($order->subtotal, false) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between">
                    <span>Diskon</span>
                    <span>-{{ format_rupiah($order->discount_amount, false) }}</span>
                </div>
            @endif
            @if($order->tax_amount > 0)
                <div class="flex justify-between">
                    <span>PPN (11%)</span>
                    <span>{{ format_rupiah($order->tax_amount, false) }}</span>
                </div>
            @endif
            @if($order->shipping_cost > 0)
                <div class="flex justify-between">
                    <span>Ongkos Kirim</span>
                    <span>{{ format_rupiah($order->shipping_cost, false) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-xs font-bold pt-1 border-t border-dotted border-black">
                <span>TOTAL</span>
                <span>{{ format_rupiah($order->grand_total, false) }}</span>
            </div>
            <div class="flex justify-between pt-1">
                <span>Metode Bayar</span>
                <span class="font-bold uppercase">{{ $order->payment_method }}</span>
            </div>
            @if($order->payment_method === 'cash')
                <div class="flex justify-between">
                    <span>Tunai Diterima</span>
                    <span>{{ format_rupiah($order->cash_received, false) }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Kembalian</span>
                    <span>{{ format_rupiah($order->change_amount, false) }}</span>
                </div>
            @endif
        </div>

        <!-- Footer Note -->
        <div class="pt-3 text-center space-y-1 text-[9px]">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar kecuali ada perjanjian.</p>
            <div class="pt-1 text-[8px] text-gray-500">
                Dicetak: {{ date('d/m/Y H:i:s') }} &bull; Cetakan ke-{{ $order->print_count }}
            </div>
            <div class="text-[8px] font-bold text-gray-700 tracking-wider">
                POWERED BY OMNIKASIR
            </div>
        </div>

    </div>

    <script>
    function receiptPage() {
        return {
            escPosResult: null,
            async testEscPosBridge() {
                try {
                    const response = await fetch("{{ route('pos.orders.print-bridge', $order) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ paper_size: '{{ $paperWidth }}mm' })
                    });
                    const data = await response.json();
                    this.escPosResult = data;
                } catch (e) {
                    alert('Gagal menjalankan simulasi bridge: ' + e.message);
                }
            }
        };
    }
    </script>
</body>
</html>
