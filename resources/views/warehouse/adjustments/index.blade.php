@extends('layouts.admin')

@section('title', 'Penyesuaian & Opname Stok')

@section('content')
<div class="space-y-6" x-data="{ adjustModal: {{ $selectedProduct ? 'true' : 'false' }} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Penyesuaian & Opname Stok</h1>
            <p class="text-sm text-slate-500">Catat restock supplier, selisih hasil stock opname fisik, barang rusak, atau retur.</p>
        </div>
        <div>
            <button @click="adjustModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Opname / Restock Baru</span>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('warehouse.adjustments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Produk</label>
                <select name="product_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }} (Stok: {{ $p->stock_qty }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Penyesuaian</label>
                <select name="adjustment_type" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Tipe --</option>
                    <option value="restock" {{ $adjustmentType == 'restock' ? 'selected' : '' }}>Restock Supplier (+)</option>
                    <option value="opname" {{ $adjustmentType == 'opname' ? 'selected' : '' }}>Stock Opname Fisik</option>
                    <option value="damaged" {{ $adjustmentType == 'damaged' ? 'selected' : '' }}>Barang Rusak / Kadaluarsa (-)</option>
                    <option value="return" {{ $adjustmentType == 'return' ? 'selected' : '' }}>Retur Pelanggan (+)</option>
                </select>
            </div>
            <div></div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('warehouse.adjustments.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Adjustments Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">No. Dokumen & Tanggal</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4 text-center">Sebelum</th>
                        <th class="py-3 px-4 text-center">Mutasi</th>
                        <th class="py-3 px-4 text-center">Sesudah</th>
                        <th class="py-3 px-4">Alasan / Catatan</th>
                        <th class="py-3 px-4">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($adjustments as $adj)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900">{{ $adj->adjustment_number }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ format_tanggal($adj->adjusted_at, 'd M Y H:i') }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $adj->product->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $adj->product->sku ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($adj->adjustment_type === 'restock')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Restock
                                    </span>
                                @elseif($adj->adjustment_type === 'opname')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-800">
                                        Opname Fisik
                                    </span>
                                @elseif($adj->adjustment_type === 'damaged')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                                        Barang Rusak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Retur
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-600 whitespace-nowrap">
                                {{ $adj->qty_before }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold whitespace-nowrap {{ $adj->qty_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $adj->qty_change > 0 ? '+' : '' }}{{ $adj->qty_change }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $adj->qty_after }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                {{ $adj->reason }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 whitespace-nowrap font-medium">
                                {{ $adj->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada riwayat penyesuaian stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>

    <!-- Adjustment Modal -->
    <div x-show="adjustModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="adjustModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Input Penyesuaian / Opname Stok</h3>
                <button @click="adjustModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('warehouse.adjustments.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Produk SKU <span class="text-rose-500">*</span></label>
                    <select name="product_id" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} (Saat ini: {{ $p->stock_qty }} {{ $p->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Penyesuaian <span class="text-rose-500">*</span></label>
                        <select name="adjustment_type" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                            <option value="restock">Restock Supplier (+)</option>
                            <option value="opname">Stock Opname Fisik (+/-)</option>
                            <option value="damaged">Barang Rusak/Kadaluarsa (-)</option>
                            <option value="return">Retur Pelanggan (+)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Perubahan Qty <span class="text-rose-500">*</span></label>
                        <input type="number" name="qty_change" placeholder="Contoh: 10 atau -3" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Modal Satuan (Rp)</label>
                    <input type="number" name="unit_cost" placeholder="Kosongkan untuk memakai harga modal tersimpan" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penyesuaian <span class="text-rose-500">*</span></label>
                    <textarea name="reason" rows="2" placeholder="Contoh: Penerimaan PO #INV-9921 dari supplier utama" required class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="adjustModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm">Simpan Mutasi</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
