<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'shift_name',
        'scheduled_start',
        'scheduled_end',
        'clock_in_at',
        'clock_out_at',
        'work_duration_minutes',
        'status',
        'clock_in_ip',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'work_duration_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'hadir' => ['label' => 'Tepat Waktu', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'terlambat' => ['label' => 'Terlambat', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            'izin' => ['label' => 'Izin', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
            'sakit' => ['label' => 'Sakit', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
            'alpha' => ['label' => 'Tanpa Keterangan', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            default => ['label' => ucfirst($this->status), 'class' => 'bg-slate-100 text-slate-800 border-slate-200'],
        };
    }
}
