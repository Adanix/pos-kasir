<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $userId = $request->input('user_id');
        $status = $request->input('status');

        $query = Attendance::with('user')
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date', 'desc')
            ->orderBy('clock_in_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $staffUsers = User::where('is_active', true)->get();

        // Stats summary
        $totalHadir = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'hadir')->count();
        $totalTerlambat = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->where('status', 'terlambat')->count();
        $totalIzinSakit = Attendance::whereBetween('attendance_date', [$startDate, $endDate])->whereIn('status', ['izin', 'sakit'])->count();

        return view('admin.attendance.index', compact(
            'attendances',
            'staffUsers',
            'startDate',
            'endDate',
            'userId',
            'status',
            'totalHadir',
            'totalTerlambat',
            'totalIzinSakit'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'attendance_date' => ['required', 'date'],
            'status' => ['required', 'in:hadir,terlambat,izin,sakit,alpha'],
            'shift_name' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $isPagi = ($validated['shift_name'] === 'Pagi');

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'shift_name' => $validated['shift_name'],
                'scheduled_start' => $isPagi ? '08:00:00' : '15:00:00',
                'scheduled_end' => $isPagi ? '16:00:00' : '22:00:00',
                'status' => $validated['status'],
                'notes' => $validated['notes'] ? '[Koreksi Admin] ' . $validated['notes'] : '[Koreksi Admin]',
            ]
        );

        return back()->with('success', "Catatan absensi karyawan {$user->name} berhasil disimpan.");
    }
}
