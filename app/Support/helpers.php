<?php

if (!function_exists('format_rupiah')) {
    /**
     * Format number as Indonesian Rupiah (e.g. Rp 125.000 or -Rp 50.000)
     */
    function format_rupiah($amount, bool $withPrefix = true, int $decimals = 0): string
    {
        $numeric = (float) ($amount ?? 0);
        $isNegative = $numeric < 0;
        $absolute = abs($numeric);
        $formatted = number_format($absolute, $decimals, ',', '.');

        if ($withPrefix) {
            return $isNegative ? '-Rp ' . $formatted : 'Rp ' . $formatted;
        }

        return $isNegative ? '-' . $formatted : $formatted;
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Format timestamp/datetime to Indonesian readable string
     */
    function format_tanggal($datetime, bool $withTime = true): string
    {
        if (!$datetime) {
            return '-';
        }

        try {
            $dt = $datetime instanceof \Carbon\Carbon
                ? $datetime
                : \Carbon\Carbon::parse($datetime);

            $bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $d = $dt->format('d');
            $m = $bulan[(int) $dt->format('m')] ?? $dt->format('M');
            $y = $dt->format('Y');

            if ($withTime) {
                return "{$d} {$m} {$y}, " . $dt->format('H:i') . ' WIB';
            }

            return "{$d} {$m} {$y}";
        } catch (\Exception $e) {
            return (string) $datetime;
        }
    }
}
