@extends('layouts.admin')

@section('title', 'Hak Akses & Role Matrix')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Matriks Hak Akses (Role Permissions)</h1>
            <p class="text-sm text-slate-500">Konfigurasi batasan akses modul untuk setiap peran staf operasional.</p>
        </div>
    </div>

    <!-- Permission Matrix Form -->
    <form method="POST" action="{{ route('admin.roles.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($roles as $roleKey)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs uppercase
                            {{ $roleKey === 'admin' ? 'bg-indigo-600 text-white' : ($roleKey === 'cashier' ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white') }}">
                            {{ substr($roleKey, 0, 2) }}
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wide">
                                Role: {{ $roleKey === 'admin' ? 'Administrator / Owner' : ($roleKey === 'cashier' ? 'Kasir (POS)' : 'Staf Gudang') }}
                            </h2>
                            <p class="text-[11px] text-slate-500">
                                {{ $roleKey === 'admin' ? 'Akses penuh ke seluruh modul sistem dan konfigurasi keuangan' : ($roleKey === 'cashier' ? 'Akses ke kasir POS, shift, dan pencatatan absensi mandiri' : 'Akses inventori, katalog produk, opname stok, dan kartu mutasi') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200/80 text-slate-400 uppercase tracking-wider text-[10px] font-semibold">
                                <th class="py-3 px-5">Nama Modul</th>
                                <th class="py-3 px-4 text-center">Lihat (View)</th>
                                <th class="py-3 px-4 text-center">Tambah (Create)</th>
                                <th class="py-3 px-4 text-center">Ubah (Edit)</th>
                                <th class="py-3 px-4 text-center">Hapus (Delete)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @if(isset($permissions[$roleKey]))
                                @foreach($permissions[$roleKey] as $perm)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-5">
                                            <span class="font-bold text-slate-900 uppercase tracking-wider font-mono text-[11px]">{{ $perm->module }}</span>
                                            <div class="text-[10px] text-slate-400">
                                                @if($perm->module === 'pos') Modul Kasir & Cetak Struk
                                                @elseif($perm->module === 'inventory') Manajemen Stok & Produk
                                                @elseif($perm->module === 'cashbook') Buku Kas Masuk/Keluar
                                                @elseif($perm->module === 'reports') Laporan Laba Rugi & Omset
                                                @elseif($perm->module === 'attendance') Absensi Shift Karyawan
                                                @elseif($perm->module === 'users') Akun & Hak Akses Staf
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" name="matrix[{{ $perm->id }}][can_view]" value="1" {{ $perm->can_view ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" name="matrix[{{ $perm->id }}][can_create]" value="1" {{ $perm->can_create ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" name="matrix[{{ $perm->id }}][can_edit]" value="1" {{ $perm->can_edit ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" name="matrix[{{ $perm->id }}][can_delete]" value="1" {{ $perm->can_delete ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="flex justify-end pt-2">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow transition">
                Simpan Konfigurasi Matriks Hak Akses
            </button>
        </div>
    </form>

</div>
@endsection
