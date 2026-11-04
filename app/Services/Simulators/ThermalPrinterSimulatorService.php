<?php

namespace App\Services\Simulators;

use App\Models\Order;
use App\Models\PrintLog;
use App\Models\User;

class ThermalPrinterSimulatorService
{
    /**
     * Generate ESC/POS raw command payload and record print log.
     */
    public function generateEscPosBridge(Order $order, string $paperSize = '58mm', ?User $user = null, bool $simulateError = false): array
    {
        if ($simulateError) {
            PrintLog::create([
                'order_id' => $order->id,
                'user_id' => $user?->id,
                'paper_size' => $paperSize,
                'printer_mode' => 'escpos_bridge',
                'status' => 'failed',
                'raw_payload' => '[ERROR_SIMULATION] Printer offline or out of paper',
                'printed_at' => now(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Simulasi gagal: Kertas printer habis atau printer thermal offline.',
            ];
        }

        $widthChars = ($paperSize === '80mm') ? 42 : 32;
        $divider = str_repeat('-', $widthChars);
        $doubleDivider = str_repeat('=', $widthChars);

        // Build text ESC/POS stream simulation
        $lines = [];
        $lines[] = "[ESC @] -- INITIALIZE PRINTER";
        $lines[] = "[ESC a 1] -- ALIGN CENTER";
        $lines[] = "[ESC E 1]OMNIKASIR NUSANTARA[ESC E 0]";
        $lines[] = "Jl. Pemuda No. 45, Semarang";
        $lines[] = "Telp/WA: 0812-8899-7766";
        $lines[] = $doubleDivider;
        $lines[] = "[ESC a 0] -- ALIGN LEFT";
        $lines[] = "No. Struk : " . $order->order_number;
        $lines[] = "Waktu     : " . $order->ordered_at->format('d/m/Y H:i:s');
        $lines[] = "Kasir     : " . ($order->cashier?->name ?? 'Kasir Utama');
        $lines[] = "Pelanggan : " . $order->customer_name;
        $lines[] = $divider;

        foreach ($order->items as $item) {
            $name = mb_strimwidth($item->product_name_snapshot, 0, $widthChars - 14, '..');
            $qtyPrice = $item->qty . 'x ' . format_rupiah($item->unit_price, false);
            $totalStr = format_rupiah($item->line_total, false);
            $spaces = max(1, $widthChars - strlen($qtyPrice) - strlen($totalStr));

            $lines[] = $name;
            $lines[] = $qtyPrice . str_repeat(' ', $spaces) . $totalStr;
        }

        $lines[] = $divider;
        $subtotalStr = format_rupiah($order->subtotal, false);
        $lines[] = "Subtotal" . str_repeat(' ', max(1, $widthChars - 8 - strlen($subtotalStr))) . $subtotalStr;

        if ($order->discount_amount > 0) {
            $discStr = '-' . format_rupiah($order->discount_amount, false);
            $lines[] = "Diskon" . str_repeat(' ', max(1, $widthChars - 6 - strlen($discStr))) . $discStr;
        }

        if ($order->tax_amount > 0) {
            $taxStr = format_rupiah($order->tax_amount, false);
            $lines[] = "PPN 11%" . str_repeat(' ', max(1, $widthChars - 7 - strlen($taxStr))) . $taxStr;
        }

        if ($order->shipping_cost > 0) {
            $shipStr = format_rupiah($order->shipping_cost, false);
            $lines[] = "Ongkir" . str_repeat(' ', max(1, $widthChars - 6 - strlen($shipStr))) . $shipStr;
        }

        $totalStr = format_rupiah($order->grand_total);
        $lines[] = "[ESC E 1]TOTAL" . str_repeat(' ', max(1, $widthChars - 5 - strlen($totalStr))) . $totalStr . "[ESC E 0]";

        if ($order->payment_method === 'cash') {
            $cashStr = format_rupiah($order->cash_received);
            $changeStr = format_rupiah($order->change_amount);
            $lines[] = "Bayar Tunai" . str_repeat(' ', max(1, $widthChars - 11 - strlen($cashStr))) . $cashStr;
            $lines[] = "Kembalian" . str_repeat(' ', max(1, $widthChars - 9 - strlen($changeStr))) . $changeStr;
        } else {
            $methodStr = strtoupper($order->payment_method);
            $lines[] = "Metode" . str_repeat(' ', max(1, $widthChars - 6 - strlen($methodStr))) . $methodStr;
            $lines[] = "Status" . str_repeat(' ', max(1, $widthChars - 6 - 5)) . "LUNAS";
        }

        $lines[] = $doubleDivider;
        $lines[] = "[ESC a 1] -- ALIGN CENTER";
        $lines[] = "Terima kasih telah berbelanja!";
        $lines[] = "Barang yang dibeli tidak dapat ditukar.";
        $lines[] = "[GS V 65 0] -- FULL PAPER CUT";

        $rawPayload = implode("\n", $lines);

        // Record print log
        PrintLog::create([
            'order_id' => $order->id,
            'user_id' => $user?->id,
            'paper_size' => $paperSize,
            'printer_mode' => 'escpos_bridge',
            'status' => 'printed',
            'raw_payload' => $rawPayload,
            'printed_at' => now(),
        ]);

        $order->increment('print_count');

        return [
            'status' => 'success',
            'order_number' => $order->order_number,
            'paper_size' => $paperSize,
            'raw_payload' => $rawPayload,
            'print_count' => $order->print_count,
            'message' => "Struk berhasil dikirim ke bridge thermal printer {$paperSize}.",
        ];
    }
}
