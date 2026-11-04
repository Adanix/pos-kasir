@extends('layouts.store')

@section('title', 'Checkout Pembayaran — OmniKasir Store')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" 
     x-data="checkoutApp({{ json_encode($shippingOptions) }}, {{ $subtotal }})">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Checkout & Pengiriman</h1>
            <p class="text-xs sm:text-sm text-slate-500">Lengkapi data penerima dan pilih metode pembayaran pesanan Anda.</p>
        </div>
        
        <!-- Pre-fill Demo Data CTA -->
        <button type="button" 
                @click="fillSampleCustomer()" 
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded-xl text-xs font-bold transition shadow-sm">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span>Isi Otomatis Data Pelanggan Demo</span>
        </button>
    </div>

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-center justify-between">
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('store.checkout.store') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf

        <!-- LEFT SECTION: Customer & Shipping & Payment -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Step 1: Customer Contact -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">1</span>
                    <h2 class="text-base font-bold text-slate-900">Data Pemesan / Pelanggan</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_name" x-model="customerName" required placeholder="Contoh: Budi Pratama" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <input type="text" name="customer_phone" x-model="customerPhone" required placeholder="08123456789" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500 font-mono">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email (Opsional)</label>
                        <input type="email" name="customer_email" x-model="customerEmail" placeholder="budi@example.com" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Step 2: Fulfillment & Shipping -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">2</span>
                    <h2 class="text-base font-bold text-slate-900">Metode Pengambilan & Ekspedisi</h2>
                </div>

                <!-- Fulfillment Radio -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition"
                           :class="fulfillmentType === 'delivery' ? 'bg-emerald-50/50 border-emerald-500 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="fulfillment_type" value="delivery" x-model="fulfillmentType" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="font-bold text-xs text-slate-900">Pengiriman Ekspedisi</div>
                            <div class="text-[11px] text-slate-500">Dikirim via JNE, J&T, SiCepat, dll.</div>
                        </div>
                    </label>

                    <label class="relative flex items-center gap-3 p-4 rounded-2xl border cursor-pointer transition"
                           :class="fulfillmentType === 'pickup' ? 'bg-emerald-50/50 border-emerald-500 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50'">
                        <input type="radio" name="fulfillment_type" value="pickup" x-model="fulfillmentType" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="font-bold text-xs text-slate-900">Ambil di Toko Fisik (BOPIS)</div>
                            <div class="text-[11px] text-slate-500">Bebas Ongkir &bull; Ambil di meja kasir</div>
                        </div>
                    </label>
                </div>

                <!-- Delivery Courier & Address Details (If Delivery) -->
                <div x-show="fulfillmentType === 'delivery'" class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih Layanan Kurir & Ongkir</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <template x-for="opt in shippingOptions" :key="opt.code">
                                <label class="p-3 rounded-xl border flex items-center justify-between cursor-pointer transition text-xs"
                                       :class="selectedCourier === opt.code ? 'bg-emerald-50/80 border-emerald-500' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="shipping_courier" :value="opt.code" x-model="selectedCourier" class="text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <div class="font-bold text-slate-900" x-text="opt.name + ' (' + opt.service + ')'"></div>
                                            <div class="text-[10px] text-slate-400" x-text="'Estimasi ' + opt.etd"></div>
                                        </div>
                                    </div>
                                    <div class="font-mono font-bold text-emerald-600 text-right" x-text="formatRupiah(opt.cost)"></div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Pengiriman <span class="text-rose-500">*</span></label>
                        <textarea name="customer_address" x-model="customerAddress" rows="2" placeholder="Nama Jalan, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Penjual / Kasir</label>
                    <input type="text" name="customer_notes" x-model="customerNotes" placeholder="Contoh: Mohon dipacking kardus tebal" class="w-full text-xs rounded-xl border-slate-200 focus:border-emerald-500">
                </div>
            </div>

            <!-- Step 3: Payment Method -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">3</span>
                    <h2 class="text-base font-bold text-slate-900">Metode Pembayaran Online</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    
                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between space-y-2"
                           :class="paymentMethod === 'qris' ? 'bg-emerald-50/60 border-emerald-500 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50'">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900">QRIS Instan</span>
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[10px] text-slate-500">Scan QR via BCA Mobile, GoPay, OVO, Dana, ShopeePay.</p>
                        <span class="text-[9px] font-bold text-emerald-600 uppercase">Otomatis Terverifikasi</span>
                    </label>

                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between space-y-2"
                           :class="paymentMethod === 'bank_transfer' ? 'bg-emerald-50/60 border-emerald-500 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50'">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900">Virtual Account</span>
                            <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[10px] text-slate-500">BCA / Mandiri / BRI VA transfer tanpa upload bukti.</p>
                        <span class="text-[9px] font-bold text-emerald-600 uppercase">Simulasi Real-time</span>
                    </label>

                    <label class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between space-y-2"
                           :class="paymentMethod === 'cod' ? 'bg-emerald-50/60 border-emerald-500 shadow-sm' : 'bg-white border-slate-200 hover:bg-slate-50'">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900">COD (Bayar Tunai)</span>
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[10px] text-slate-500">Bayar saat kurir mengantar barang atau saat ambil di kasir.</p>
                        <span class="text-[9px] font-bold text-slate-500 uppercase">Konfirmasi Manual</span>
                    </label>

                </div>
            </div>

        </div>

        <!-- RIGHT SECTION: Order Review & Submit CTA -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5 sticky top-24">
                <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Ringkasan Pesanan</h3>

                <!-- Cart Items Miniature -->
                <div class="max-h-48 overflow-y-auto divide-y divide-slate-100 space-y-2 text-xs pr-1">
                    @foreach($cartItems as $it)
                        <div class="pt-2 flex justify-between gap-2">
                            <div class="flex-1">
                                <span class="font-bold text-slate-800">{{ $it['name'] }}</span>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $it['qty'] }}x @ {{ format_rupiah($it['unit_price']) }}</div>
                            </div>
                            <span class="font-mono font-semibold text-slate-800 whitespace-nowrap">{{ format_rupiah($it['line_total']) }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Costs Breakdown -->
                <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Belanja:</span>
                        <span class="font-mono font-semibold" x-text="formatRupiah(subtotal)"></span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim:</span>
                        <span class="font-mono font-semibold text-emerald-600" x-text="formatRupiah(currentShippingCost)"></span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-between items-baseline">
                        <span class="text-sm font-bold text-slate-900">Total Pembayaran:</span>
                        <span class="text-2xl font-black font-mono text-emerald-600" x-text="formatRupiah(grandTotal)"></span>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Konfirmasi & Bayar Sekarang &rarr;</span>
                </button>

                <p class="text-[10px] text-center text-slate-400">
                    Dengan menekan tombol di atas, pesanan akan dibuat dan stok di gudang/POS otomatis ter-reservasi.
                </p>
            </div>
        </div>

    </form>

</div>

<script>
function checkoutApp(shippingOptions, subtotal) {
    return {
        customerName: '',
        customerPhone: '',
        customerEmail: '',
        customerAddress: '',
        customerNotes: '',
        fulfillmentType: 'delivery',
        shippingOptions: shippingOptions || [],
        selectedCourier: shippingOptions.length > 0 ? shippingOptions[0].code : 'jne_reg',
        paymentMethod: 'qris',
        subtotal: subtotal || 0,

        fillSampleCustomer() {
            this.customerName = 'Budi Pratama';
            this.customerPhone = '081298765432';
            this.customerEmail = 'budi.pratama@gmail.com';
            this.customerAddress = 'Jl. Pandanaran No. 88, RT 02/RW 04, Semarang Tengah, Kota Semarang, Jawa Tengah 50134';
            this.customerNotes = 'Mohon packing kardus rapi dan jangan dibanting.';
        },

        get currentShippingCost() {
            if (this.fulfillmentType === 'pickup') {
                return 0;
            }
            const found = this.shippingOptions.find(o => o.code === this.selectedCourier);
            return found ? found.cost : 0;
        },

        get grandTotal() {
            return this.subtotal + this.currentShippingCost;
        },

        formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }
    };
}
</script>
@endsection
