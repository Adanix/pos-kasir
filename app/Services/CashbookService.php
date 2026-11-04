<?php

namespace App\Services;

use App\Models\CashbookEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CashbookService
{
    /**
     * Record a new entry in the Daily Cashbook (Buku Kas) with atomic running balance.
     */
    public function recordEntry(
        string $type,
        string $category,
        float $amount,
        string $description,
        string $paymentMethod = 'cash',
        ?string $referenceNumber = null,
        ?int $orderId = null,
        ?int $posShiftId = null,
        ?int $userId = null,
        ?string $entryDate = null
    ): CashbookEntry {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Nominal buku kas harus lebih besar dari 0.');
        }

        $type = strtolower($type);
        if (!in_array($type, ['in', 'out'], true)) {
            throw new \InvalidArgumentException('Tipe buku kas harus "in" atau "out".');
        }

        $date = $entryDate ? Carbon::parse($entryDate)->toDateString() : now()->toDateString();
        $transactionAt = now();

        return DB::transaction(function () use (
            $type, $category, $amount, $description, $paymentMethod,
            $referenceNumber, $orderId, $posShiftId, $userId, $date, $transactionAt
        ) {
            // Get latest running balance with row lock
            $latestEntry = CashbookEntry::orderBy('id', 'desc')->lockForUpdate()->first();
            $currentBalance = $latestEntry ? (float) $latestEntry->balance_after : 0.0;

            $newBalance = ($type === 'in')
                ? $currentBalance + $amount
                : $currentBalance - $amount;

            $entryNumber = 'KAS-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            return CashbookEntry::create([
                'entry_number' => $entryNumber,
                'entry_date' => $date,
                'transaction_at' => $transactionAt,
                'type' => $type,
                'category' => $category,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'description' => $description,
                'reference_number' => $referenceNumber,
                'order_id' => $orderId,
                'pos_shift_id' => $posShiftId,
                'created_by' => $userId,
            ]);
        });
    }

    /**
     * Get summary metrics for a specific date range.
     */
    public function getSummary(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Saldo Awal Periode: Net sum of entries prior to startDate
        $priorIn = (float) CashbookEntry::where('entry_date', '<', $startDate)
            ->where('type', 'in')
            ->sum('amount');
        $priorOut = (float) CashbookEntry::where('entry_date', '<', $startDate)
            ->where('type', 'out')
            ->sum('amount');
        $openingBalance = $priorIn - $priorOut;

        // Current period inflows and outflows
        $totalIn = (float) CashbookEntry::whereBetween('entry_date', [$startDate, $endDate])
            ->where('type', 'in')
            ->sum('amount');

        $totalOut = (float) CashbookEntry::whereBetween('entry_date', [$startDate, $endDate])
            ->where('type', 'out')
            ->sum('amount');

        $closingBalance = $openingBalance + $totalIn - $totalOut;

        // Category breakdown
        $categoryBreakdown = CashbookEntry::whereBetween('entry_date', [$startDate, $endDate])
            ->select('type', 'category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type', 'category')
            ->get();

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'opening_balance' => $openingBalance,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'closing_balance' => $closingBalance,
            'category_breakdown' => $categoryBreakdown,
        ];
    }
}
