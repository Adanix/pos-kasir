<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     * $roles can be comma-separated list of allowed roles, e.g. "admin,cashier"
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Silakan masuk terlebih dahulu.'], 401);
            }
            return redirect()->route('demo.login');
        }

        // Flatten in case roles are comma-separated string or array
        $allowed = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowed[] = $trimmed;
                }
            }
        }

        if (!empty($allowed) && !in_array($user->role, $allowed, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akses ditolak: Peran Anda tidak memiliki izin untuk tindakan ini.'], 403);
            }

            abort(403, 'Akses Ditolak — Peran Anda (' . ($user->role ?? 'N/A') . ') tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
