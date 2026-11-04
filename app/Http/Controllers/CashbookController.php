<?php

namespace App\Http\Controllers;

use App\Models\CashbookEntry;
use App\Services\CashbookService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashbookController extends Controller
{
    public function __construct(
        protected CashbookService $cashbookService
    ) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $category = $request->input('category');
        $type = $request->input('type');

        $summary = $this->cashbookService->getSummary($startDate, $endDate);

        $query = CashbookEntry::with(['creator', 'order', 'posShift'])
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->orderBy('transaction_at', 'desc')
            ->orderBy('id', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $entries = $query->paginate(20)->withQueryString();

        return view('admin.cashbook.index', compact('summary', 'entries', 'startDate', 'endDate', 'category', 'type'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:in,out'],
            'category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:100'],
            'description' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cash,qris,bank_transfer'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'entry_date' => ['nullable', 'date'],
        ]);

        $this->cashbookService->recordEntry(
            type: $validated['type'],
            category: $validated['category'],
            amount: (float) $validated['amount'],
            description: $validated['description'],
            paymentMethod: $validated['payment_method'],
            referenceNumber: $validated['reference_number'] ?? null,
            userId: $request->user()->id,
            entryDate: $validated['entry_date'] ?? null
        );

        return redirect()->route('admin.cashbook.index')
            ->with('success', 'Transaksi Buku Kas berhasil dicatat.');
    }
}
