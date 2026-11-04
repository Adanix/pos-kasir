<?php

namespace App\Services\Simulators;

class DemoShippingService
{
    /**
     * Get available shipping options for online store.
     */
    public function getAvailableOptions(int $weightGrams = 500): array
    {
        $weightKg = max(1, ceil($weightGrams / 1000));

        return [
            [
                'code' => 'pickup',
                'name' => 'Ambil Sendiri di Toko',
                'service' => 'Bebas Antre (Pick-up Counter)',
                'etd' => 'Bisa diambil hari ini',
                'cost' => 0,
                'formatted_cost' => 'Gratis',
            ],
            [
                'code' => 'instant',
                'name' => 'Kurir Instan (Dalam Kota)',
                'service' => 'Pengiriman Cepat (Maks. 3 Jam)',
                'etd' => '1 - 3 Jam',
                'cost' => 15000 + (($weightKg - 1) * 5000),
                'formatted_cost' => format_rupiah(15000 + (($weightKg - 1) * 5000)),
            ],
            [
                'code' => 'jne_reg',
                'name' => 'JNE Reguler',
                'service' => 'Reguler Antar Kota',
                'etd' => '2 - 3 Hari',
                'cost' => 18000 * $weightKg,
                'formatted_cost' => format_rupiah(18000 * $weightKg),
            ],
            [
                'code' => 'sicepat_best',
                'name' => 'SiCepat BEST',
                'service' => 'Besok Sampai Tujuan (Express)',
                'etd' => '1 Hari Kerja',
                'cost' => 24000 * $weightKg,
                'formatted_cost' => format_rupiah(24000 * $weightKg),
            ],
        ];
    }

    /**
     * Calculate cost for selected courier.
     */
    public function calculateCost(string $courierCode, int $weightGrams = 500): float
    {
        $options = $this->getAvailableOptions($weightGrams);
        foreach ($options as $opt) {
            if ($opt['code'] === $courierCode) {
                return (float) $opt['cost'];
            }
        }

        return 0.0;
    }

    /**
     * Generate simulated tracking number.
     */
    public function generateTrackingNumber(string $courierCode): string
    {
        $prefix = strtoupper(str_replace('_', '', $courierCode));
        return "RESI-{$prefix}-" . date('Ymd') . '-' . mt_rand(100000, 999999);
    }

    public function generateWaybill(string $courierCode): string
    {
        return $this->generateTrackingNumber($courierCode);
    }
}
