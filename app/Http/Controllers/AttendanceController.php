<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Show personal employee attendance dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderBy('attendance_date', 'desc')
            ->take(15)
            ->get();

        return view('attendance.index', compact('user', 'todayAttendance', 'recentAttendances'));
    }

    /**
     * Handle Clock-In (Absen Masuk).
     */
    public function clockIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = now();

        $existing = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing) {
            return back()->with('info', "Anda sudah melakukan Absen Masuk hari ini pada pukul {$existing->clock_in_at->format('H:i')} WIB.");
        }

        $shiftName = $request->input('shift_name', ucfirst($user->default_shift ?? 'Pagi'));
        $scheduledStart = ($shiftName === 'Sore') ? '15:00:00' : '08:00:00';
        $scheduledEnd = ($shiftName === 'Sore') ? '22:00:00' : '16:00:00';

        // Check if late (more than 15 minutes past scheduled start)
        $targetStart = Carbon::today()->setTimeFromTimeString($scheduledStart)->addMinutes(15);
        $status = $now->greaterThan($targetStart) ? 'terlambat' : 'hadir';

        Attendance::create([
            'user_id' => $user->id,
            'attendance_date' => $today,
            'shift_name' => $shiftName,
            'scheduled_start' => $scheduledStart,
            'scheduled_end' => $scheduledEnd,
            'clock_in_at' => $now,
            'clock_out_at' => null,
            'work_duration_minutes' => 0,
            'status' => $status,
            'clock_in_ip' => $request->ip(),
            'notes' => $request->input('notes'),
        ]);

        $statusMsg = ($status === 'terlambat') ? ' (Status: Terlambat)' : ' (Tepat Waktu)';

        return back()->with('success', "Absen Masuk berhasil dicatat pada pukul {$now->format('H:i')} WIB{$statusMsg}.");
    }

    /**
     * Handle Clock-Out (Absen Pulang).
     */
    public function clockOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        $today = Carbon::today()->toDateString();
        $now = now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in_at) {
            return back()->with('error', 'Anda belum melakukan Absen Masuk hari ini.');
        }

        if ($attendance->clock_out_at) {
            return back()->with('info', "Anda sudah melakukan Absen Pulang hari ini pada pukul {$attendance->clock_out_at->format('H:i')} WIB.");
        }

        $durationMinutes = $attendance->clock_in_at->diffInMinutes($now);

        $attendance->clock_out_at = $now;
        $attendance->work_duration_minutes = $durationMinutes;
        if ($request->filled('notes')) {
            $attendance->notes = ($attendance->notes ? $attendance->notes . ' | ' : '') . $request->input('notes');
        }
        $attendance->save();

        $hours = floor($durationMinutes / 60);
        $mins = $durationMinutes % 60;

        return back()->with('success', "Absen Pulang berhasil dicatat pada pukul {$now->format('H:i')} WIB. Total jam kerja: {$hours} jam {$mins} menit.");
    }
}
