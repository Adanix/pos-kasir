@extends('layouts.admin')

@section('title', 'Kartu Stok (Log Mutasi Inventori)')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kartu Stok (Log Mutasi Inventori)</h1>
            <p class="text-sm text-slate-500">Audit trail pencatatan mutasi masuk dan keluar barang secara real-time dan immutable.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('warehouse.adjustments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Opname / Restock</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('warehouse.movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Filter Produk</label>
                <select name="product_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }} (SKU: {{ $p->sku }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Mutasi</label>
                <select name="movement_type" class="w-full text-xs rounded-xl border-slate-200 focus:border-amber-500">
                    <option value="">-- Semua Tipe --</option>
                    <option value="pos_sale" {{ $movementType == 'pos_sale' ? 'selected' : '' }}>Penjualan Kasir POS</option>
                    <option value="pos_sale_offline_sync" {{ $movementType == 'pos_sale_offline_sync' ? 'selected' : '' }}>Sinkronisasi Offline POS</option>
                    <option value="online_sale" {{ $movementType == 'online_sale' ? 'selected' : '' }}>Penjualan Toko Online</option>
                    <option value="restock" {{ $movementType == 'restock' ? 'selected' : '' }}>Penerimaan Restock (+)</option>
                    <option value="opname_adjustment" {{ $movementType == 'opname_adjustment' ? 'selected' : '' }}>Opname Penyesuaian</option>
                    <option value="damaged" {{ $movementType == 'damaged' ? 'selected' : '' }}>Barang Rusak (-)</option>
                    <option value="return_refund" {{ $movementType == 'return_refund' ? 'selected' : '' }}>Retur Refund (+)</option>
                </select>
            </div>
            <div></div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('warehouse.movements.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Produk & SKU</th>
                        <th class="py-3 px-4">Tipe Transaksi</th>
                        <th class="py-3 px-4 text-center">Stok Awal</th>
                        <th class="py-3 px-4 text-center">Perubahan</th>
                        <th class="py-3 px-4 text-center">Stok Akhir</th>
                        <th class="py-3 px-4">Keterangan / Ref</th>
                        <th class="py-3 px-4">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movements as $m)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono text-slate-500 whitespace-nowrap">
                                {{ format_tanggal($m->created_at, 'd M Y H:i:s') }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $m->product->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">SKU: {{ $m->product->sku ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if(str_contains($m->movement_type, 'sale'))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ str_replace('_', ' ', strtoupper($m->movement_type)) }}
                                    </span>
                                @elseif($m->movement_type === 'restock' || $m->qty_change > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ str_replace('_', ' ', strtoupper($m->movement_type)) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ str_replace('_', ' ', strtoupper($m->movement_type)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center font-mono text-slate-600 whitespace-nowrap">
                                {{ $m->qty_before }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold whitespace-nowrap {{ $m->qty_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $m->qty_change > 0 ? '+' : '' }}{{ $m->qty_change }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $m->qty_after }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-xs truncate">
                                {{ $m->notes ?: '—' }}
                                @if($m->reference_type)
                                    <span class="text-[10px] text-slate-400 font-mono block">Ref: {{ $m->reference_type }} #{{ $m->reference_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700 whitespace-nowrap font-medium">
                                {{ $m->user->name ?? 'Sistem' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada catatan mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
