<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Kasir Terminal') — OmniKasir</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col antialiased selection:bg-emerald-500 selection:text-white bg-slate-950">

    <!-- Top Demo Banner -->
    @include('components.demo-banner')

    <!-- POS Top Navigation Bar -->
    <header class="bg-slate-900 border-b border-slate-800 text-white select-none z-30 shrink-0">
        <div class="px-4 py-2.5 flex items-center justify-between gap-3">
            
            <!-- Left: Brand & Shift Status -->
            <div class="flex items-center gap-3">
                <a href="{{ route('pos.index') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold tracking-tight text-white block leading-tight">OmniKasir POS</span>
                        <span class="text-[10px] text-emerald-400 font-mono leading-none">v1.0 Offline-First</span>
                    </div>
                </a>

                @php
                    $activeShift = \App\Models\PosShift::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
                @endphp

                <a href="{{ route('pos.shift.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border transition {{ $activeShift ? 'bg-emerald-950/60 text-emerald-300 border-emerald-800/80 hover:bg-emerald-900/60' : 'bg-amber-950/60 text-amber-300 border-amber-800/80 hover:bg-amber-900/60' }}">
                    <span class="w-2 h-2 rounded-full {{ $activeShift ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                    <span>{{ $activeShift ? 'Shift ' . $activeShift->shift_name . ' Aktif' : 'Buka Shift Baru' }}</span>
                </a>
            </div>

            <!-- Middle / Right: Quick Navigation, Online Orders, Offline Mode & Profile -->
            <div class="flex items-center gap-2 sm:gap-3">

                <!-- Online Order Queue Badge -->
                @php
                    $pendingOnlineOrdersCount = \App\Models\Order::where('channel', 'online')
                        ->whereIn('status', ['pending_payment', 'paid_confirmed', 'processing'])
                        ->count();
                @endphp
                <a href="{{ route('pos.online-orders.index') }}" class="relative inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="hidden md:inline">Pesanan Online</span>
                    @if($pendingOnlineOrdersCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-sky-500 text-slate-950 font-bold text-[10px]">
                            {{ $pendingOnlineOrdersCount }}
                        </span>
                    @endif
                </a>

                <!-- Riwayat Transaksi -->
                <a href="{{ route('pos.transactions.index') }}" class="hidden lg:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Riwayat Struk</span>
                </a>

                <!-- User Profile & Backoffice link -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-800 text-xs">
                    <div class="hidden md:block text-right">
                        <div class="font-bold text-slate-200 leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 uppercase font-mono">{{ Auth::user()->role }}</div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" title="Kembali ke Backoffice" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('attendance.index') }}" title="Presensi Staf" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-hidden flex flex-col">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
