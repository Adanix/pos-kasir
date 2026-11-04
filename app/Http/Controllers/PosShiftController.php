<?php

namespace App\Http\Controllers;

use App\Models\PosShift;
use App\Services\CashbookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PosShiftController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        $activeShift = PosShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $pastShifts = PosShift::where('user_id', $user->id)
            ->where('status', 'closed')
            ->latest('closed_at')
            ->take(10)
            ->get();

        return view('pos.shift.index', compact('activeShift', 'pastShifts'));
    }

    public function open(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if there's already an active shift
        $existing = PosShift::where('user_id', $user->id)->where('status', 'open')->first();
        if ($existing) {
            return back()->with('error', 'Anda masih memiliki shift yang sedang aktif.');
        }

        $validated = $request->validate([
            'shift_name' => ['required', 'string', 'in:Pagi,Sore,Malam'],
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $openingCash = (float) $validated['opening_cash'];
        $shiftCode = 'SHF-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $shift = PosShift::create([
            'shift_code' => $shiftCode,
            'user_id' => $user->id,
            'shift_name' => $validated['shift_name'],
            'opening_cash' => $openingCash,
            'cash_sales_total' => 0,
            'non_cash_sales_total' => 0,
            'petty_cash_out_total' => 0,
            'closing_cash_expected' => $openingCash,
            'status' => 'open',
            'notes' => $validated['notes'],
            'opened_at' => now(),
        ]);

        // Record opening cash float in cashbook
        if ($openingCash > 0) {
            $this->cashbookService->recordEntry(
                type: 'in',
                category: 'modal_awal',
                amount: $openingCash,
                description: "Modal Awal Kasir Shift {$shift->shift_name} ({$shift->shift_code})",
                paymentMethod: 'cash',
                referenceNumber: $shift->shift_code,
                posShiftId: $shift->id,
                userId: $user->id
            );
        }

        return redirect()->route('pos.index')->with('success', "Shift {$shift->shift_name} berhasil dibuka dengan kas awal " . format_rupiah($openingCash));
    }

    public function close(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $shift = PosShift::where('user_id', $user->id)->where('status', 'open')->latest()->firstOrFail();

        $validated = $request->validate([
            'closing_cash_actual' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $actual = (float) $validated['closing_cash_actual'];
        $expected = (float) $shift->closing_cash_expected;
        $diff = $actual - $expected;

        $shift->update([
            'closing_cash_actual' => $actual,
            'difference_amount' => $diff,
            'status' => 'closed',
            'notes' => $validated['notes'] ? ($shift->notes ? $shift->notes . ' | ' . $validated['notes'] : $validated['notes']) : $shift->notes,
            'closed_at' => now(),
        ]);

        return redirect()->route('pos.shift.index')->with('success', "Shift {$shift->shift_name} telah resmi ditutup.");
    }

    public function recordPettyExpense(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $shift = PosShift::where('user_id', $user->id)->where('status', 'open')->latest()->firstOrFail();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1000'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $amount = (float) $validated['amount'];

        $shift->increment('petty_cash_out_total', $amount);
        $shift->closing_cash_expected = $shift->opening_cash + $shift->cash_sales_total - $shift->petty_cash_out_total;
        $shift->save();

        // Record into cashbook
        $this->cashbookService->recordEntry(
            type: 'out',
            category: 'operasional_kasir',
            amount: $amount,
            description: "Kas Keluar Kasir: {$validated['description']}",
            paymentMethod: 'cash',
            referenceNumber: $shift->shift_code,
            posShiftId: $shift->id,
            userId: $user->id
        );

        return back()->with('success', 'Pengeluaran kas kecil kasir berhasil dicatat: ' . format_rupiah($amount));
    }
}
