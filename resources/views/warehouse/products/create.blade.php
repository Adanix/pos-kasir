@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Produk SKU Baru</h1>
            <p class="text-sm text-slate-500">Daftarkan SKU baru dengan spesifikasi fisik, barcode, dan harga.</p>
        </div>
        <a href="{{ route('warehouse.products.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <form method="POST" action="{{ route('warehouse.products.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Kertas Kasir Thermal Paper 58x30mm" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Produk <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Unit <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" placeholder="pcs, pack, roll, box, botol" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode SKU <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Contoh: ACC-THM-001" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Barcode / EAN-13 <span class="text-rose-500">*</span></label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Contoh: 8991234567890" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                </div>
            </div>

            <!-- Price & Stock -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Harga & Stok Awal</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Modal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="cost_price" value="{{ old('cost_price', 0) }}" min="0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Jual Kasir/Toko (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', 0) }}" min="0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stok Awal Fisik <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Batas Minimum Stok <span class="text-rose-500">*</span></label>
                        <input type="number" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                    </div>
                </div>
            </div>

            <!-- Online Storefront Settings -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Informasi Toko Online & Ekspedisi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Berat Barang (Gram) <span class="text-rose-500">*</span></label>
                        <input type="number" name="weight_grams" value="{{ old('weight_grams', 250) }}" min="1" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                        <span class="text-[10px] text-slate-400">Digunakan untuk kalkulasi ongkos kirim toko online.</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">URL Gambar Produk</label>
                        <input type="text" name="image_url" value="{{ old('image_url') }}" placeholder="https://images.unsplash.com/..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Produk</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan spesifikasi, keunggulan, atau cara penggunaan produk..." class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Toggles -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_online_listed" value="1" checked class="rounded text-amber-600 focus:ring-amber-500">
                    <span class="text-xs font-medium text-slate-700">Tampilkan di Katalog Toko Online Publik</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-600 focus:ring-amber-500">
                    <span class="text-xs font-medium text-slate-700">Status Aktif (Bisa Ditransaksikan)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('warehouse.products.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
