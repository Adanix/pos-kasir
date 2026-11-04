<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemoQuickLoginRequest;
use App\Models\User;
use Database\Seeders\OmniKasirDemoSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoAuthController extends Controller
{
    /**
     * Display the demo persona selection page.
     */
    public function showLogin(): View
    {
        $personas = config('demo.personas', []);
        $currentUser = Auth::user();

        return view('demo.login', compact('personas', 'currentUser'));
    }

    /**
     * Handle one-click demo quick login.
     */
    public function quickLogin(DemoQuickLoginRequest $request): RedirectResponse
    {
        $personaKey = $request->input('persona');
        $personaConfig = config("demo.personas.{$personaKey}");

        if (!$personaConfig || empty($personaConfig['email'])) {
            return back()->withErrors(['persona' => 'Konfigurasi peran demo tidak ditemukan.']);
        }

        $user = User::where('email', $personaConfig['email'])
            ->where('is_active', true)
            ->first();

        if (!$user) {
            return back()->withErrors(['persona' => 'Akun untuk peran demo ini belum terdaftar di database. Silakan jalankan seeder.']);
        }

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        $redirectUrl = $personaConfig['redirect'] ?? '/';

        return redirect()->intended($redirectUrl)
            ->with('success', "Berhasil masuk sebagai {$personaConfig['role_label']} ({$user->name}).");
    }

    /**
     * Log out current user and invalidate session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (config('demo.enabled', true)) {
            return redirect()->route('demo.login')->with('info', 'Anda telah keluar dari sesi demo.');
        }

        return redirect()->route('public.home');
    }

    /**
     * Reset demo data to initial seeded state.
     */
    public function resetDemoData(Request $request): RedirectResponse
    {
        if (!config('demo.enabled', true)) {
            abort(404);
        }

        $currentUser = Auth::user();
        $currentRole = $currentUser?->role ?? 'admin';

        // Run the seeder
        $seeder = new OmniKasirDemoSeeder();
        $seeder->run();

        // Re-authenticate the active persona
        $personaKey = match ($currentRole) {
            'cashier' => 'cashier',
            'warehouse' => 'warehouse',
            default => 'admin',
        };

        $mappedEmail = config("demo.personas.{$personaKey}.email", 'owner@omnikasir.demo');
        $freshUser = User::where('email', $mappedEmail)->first();

        if ($freshUser) {
            Auth::guard('web')->login($freshUser);
            $request->session()->regenerate();
        }

        return back()->with('success', 'Data demo berhasil di-reset ke kondisi awal!');
    }
}
