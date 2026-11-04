@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Staf')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editUser: {} }">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Manajemen Pengguna & Staf</h1>
            <p class="text-sm text-slate-500">Kelola akun staf kasir, gudang, dan owner/admin beserta shift operasional.</p>
        </div>
        <div>
            <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Pengguna Baru</span>
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Nama & Email</th>
                        <th class="py-3 px-4">Kode Staf</th>
                        <th class="py-3 px-4">Role Akses</th>
                        <th class="py-3 px-4">Shift Standar</th>
                        <th class="py-3 px-4">No. HP</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium text-slate-700">
                                {{ $u->employee_code }}
                            </td>
                            <td class="py-3 px-4">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-800">
                                        Owner / Admin
                                    </span>
                                @elseif($u->role === 'cashier')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        Kasir POS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        Gudang
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                Shift {{ $u->default_shift }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">
                                {{ $u->phone ?: '—' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($u->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <button @click="editUser = {{ json_encode($u) }}; editModal = true" class="px-2.5 py-1 text-indigo-600 hover:text-indigo-800 font-semibold hover:bg-indigo-50 rounded-lg transition">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada akun pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create User Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="createModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Tambah Staf / Pengguna</h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Staf (NIK)</label>
                        <input type="text" name="employee_code" placeholder="Contoh: KAS-003" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" placeholder="08123456789" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Role Hak Akses</label>
                        <select name="role" required class="w-full text-xs rounded-xl border-slate-200">
                            <option value="cashier">Kasir (POS)</option>
                            <option value="warehouse">Staf Gudang (Warehouse)</option>
                            <option value="admin">Owner / Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Standar</label>
                        <select name="default_shift" required class="w-full text-xs rounded-xl border-slate-200">
                            <option value="Pagi">Pagi (08:00 - 16:00)</option>
                            <option value="Sore">Sore (15:00 - 22:00)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Password)</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="create_active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="create_active" class="text-xs text-slate-700 font-medium">Akun aktif dan dapat masuk ke aplikasi</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="createModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="editModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Edit Data Staf</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + editUser.id" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="editUser.name" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Staf (NIK)</label>
                        <input type="text" name="employee_code" x-model="editUser.employee_code" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="editUser.email" required class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" x-model="editUser.phone" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Role Hak Akses</label>
                        <select name="role" x-model="editUser.role" required class="w-full text-xs rounded-xl border-slate-200">
                            <option value="cashier">Kasir (POS)</option>
                            <option value="warehouse">Staf Gudang (Warehouse)</option>
                            <option value="admin">Owner / Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Standar</label>
                        <select name="default_shift" x-model="editUser.default_shift" required class="w-full text-xs rounded-xl border-slate-200">
                            <option value="Pagi">Pagi (08:00 - 16:00)</option>
                            <option value="Sore">Sore (15:00 - 22:00)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" minlength="6" placeholder="Biarkan kosong jika tidak diganti" class="w-full text-xs rounded-xl border-slate-200 focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_active" value="1" :checked="editUser.is_active" class="rounded text-indigo-600 focus:ring-indigo-500">
                    <label for="edit_active" class="text-xs text-slate-700 font-medium">Akun aktif</label>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
