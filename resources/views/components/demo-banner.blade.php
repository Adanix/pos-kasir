@if(config('demo.enabled', true))
<div x-data="{ open: true }" x-show="open" class="relative z-50 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white text-xs py-2 px-3 sm:px-4 border-b border-indigo-500/30 shadow-md">
    <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
        <!-- Left: Status & Current Persona -->
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                Mode Demo Aktif
            </span>
            <span class="hidden sm:inline text-slate-300">|</span>
            <span class="text-slate-300 text-[11px]">
                Masuk sebagai: 
                <strong class="text-white font-medium">
                    @auth
                        {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})
                    @else
                        Pengunjung Publik
                    @endauth
                </strong>
            </span>
        </div>

        <!-- Center: Quick Persona Switcher Buttons -->
        <div class="flex items-center flex-wrap gap-1.5">
            <span class="text-slate-400 text-[11px] hidden md:inline">Ganti Peran:</span>
            
            <!-- Admin -->
            <form action="{{ route('demo.quick-login') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="persona" value="admin">
                <button type="submit" class="px-2 py-1 rounded transition text-[11px] font-medium {{ Auth::check() && Auth::user()->role === 'admin' ? 'bg-indigo-600 text-white shadow' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                    👑 Pemilik / Admin
                </button>
            </form>

            <!-- Cashier -->
            <form action="{{ route('demo.quick-login') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="persona" value="cashier">
                <button type="submit" class="px-2 py-1 rounded transition text-[11px] font-medium {{ Auth::check() && Auth::user()->role === 'cashier' ? 'bg-emerald-600 text-white shadow' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                    💳 Kasir (POS)
                </button>
            </form>

            <!-- Warehouse -->
            <form action="{{ route('demo.quick-login') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="persona" value="warehouse">
                <button type="submit" class="px-2 py-1 rounded transition text-[11px] font-medium {{ Auth::check() && Auth::user()->role === 'warehouse' ? 'bg-amber-600 text-white shadow' : 'bg-slate-800 hover:bg-slate-700 text-slate-300' }}">
                    📦 Staf Gudang
                </button>
            </form>

            <!-- Storefront -->
            <a href="{{ route('store.catalog') }}" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 transition text-[11px] font-medium">
                🛍️ Toko Online
            </a>
        </div>

        <!-- Right: Actions (Reset, Docs, Logout) -->
        <div class="flex items-center space-x-2">
            <!-- Reset Button -->
            <form action="{{ route('demo.reset') }}" method="POST" class="inline" onsubmit="return confirm('Reset seluruh data transaksi, stok, dan absensi ke kondisi awal?');">
                @csrf
                <button type="submit" title="Kembalikan data demo ke kondisi awal seeder" class="text-slate-400 hover:text-amber-300 transition flex items-center gap-1 text-[11px]">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="hidden lg:inline">Reset Data</span>
                </button>
            </form>

            <a href="{{ route('public.docs') }}" class="text-slate-400 hover:text-white transition text-[11px] flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span class="hidden sm:inline">Docs</span>
            </a>

            @auth
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-rose-400 hover:text-rose-300 font-medium text-[11px]">
                    Keluar
                </button>
            </form>
            @endauth
        </div>
    </div>
</div>
@endif
