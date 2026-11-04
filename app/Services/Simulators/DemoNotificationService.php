<?php

namespace App\Services\Simulators;

use App\Models\NotificationLog;
use App\Models\Order;

class DemoNotificationService
{
    /**
     * Send simulated WhatsApp receipt & notification for order creation / payment.
     */
    public function sendOrderCreatedNotification(Order $order): NotificationLog
    {
        $recipient = $order->customer_phone ?: '0812-3456-7890';
        $itemsList = "";
        foreach ($order->items as $item) {
            $itemsList .= "• {$item->product_name_snapshot} ({$item->qty}x) = " . format_rupiah($item->line_total) . "\n";
        }

        $message = "Halo *{$order->customer_name}*! Terima kasih telah berbelanja di *OmniKasir Nusantara*.\n\n" .
            "Pesanan Anda telah kami terima:\n" .
            "📦 *Nomor Pesanan:* `{$order->order_number}`\n" .
            "📅 *Waktu:* " . format_tanggal($order->ordered_at) . "\n\n" .
            "*Rincian Barang:*\n" . $itemsList . "\n" .
            "💰 *Total Tagihan:* *" . format_rupiah($order->grand_total) . "*\n" .
            "💳 *Metode:* " . strtoupper($order->payment_method) . "\n\n" .
            "Lacak status pesanan Anda secara berkala di: " . url("/store/orders/{$order->order_number}") . "\n\n" .
            "_Pesan ini adalah simulasi otomatis sistem OmniKasir._";

        return NotificationLog::create([
            'order_id' => $order->id,
            'channel' => 'whatsapp_sim',
            'recipient' => $recipient,
            'subject' => "Pesanan Diterima #{$order->order_number}",
            'message_body' => $message,
            'status' => 'sent_simulated',
            'sent_at' => now(),
        ]);
    }

    /**
     * Send simulated WhatsApp receipt for payment confirmation.
     */
    public function sendPaymentReceiptNotification(Order $order): NotificationLog
    {
        $recipient = $order->customer_phone ?: '0812-3456-7890';

        $message = "✅ *PEMBAYARAN DITERIMA*\n\n" .
            "Halo *{$order->customer_name}*, pembayaran untuk pesanan *#{$order->order_number}* sebesar *" .
            format_rupiah($order->grand_total) . "* telah berhasil diverifikasi.\n\n" .
            "Pesanan Anda saat ini sedang disiapkan oleh tim toko kami.\n" .
            "E-Struk & Pelacakan: " . url("/store/orders/{$order->order_number}") . "\n\n" .
            "Terima kasih telah berbelanja di *OmniKasir Nusantara*!";

        return NotificationLog::create([
            'order_id' => $order->id,
            'channel' => 'whatsapp_sim',
            'recipient' => $recipient,
            'subject' => "Pembayaran Lunas #{$order->order_number}",
            'message_body' => $message,
            'status' => 'sent_simulated',
            'sent_at' => now(),
        ]);
    }
}
