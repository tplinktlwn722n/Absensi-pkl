<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class DashboardComponent extends Component
{
    use AttendanceDetailTrait;

    public $selectedUserId = null;
    public $showUserDetailModal = false;
    public $userAttendances = [];

    public function showUserDetail($userId)
    {
        $this->selectedUserId = $userId;
        $this->showUserDetailModal = true;
        
        // Get all attendances for this user
        $this->userAttendances = Attendance::where('user_id', $userId)
            ->whereNotNull('photo')
            ->orderBy('date', 'desc')
            ->orderBy('time_in', 'desc')
            ->take(30) // Last 30 attendances with photos
            ->get();
    }

    public function closeUserDetailModal()
    {
        $this->showUserDetailModal = false;
        $this->selectedUserId = null;
        $this->userAttendances = [];
    }

    public function render()
    {
        /** @var Collection<Attendance>  */
        $attendances = Attendance::where('date', date('Y-m-d'))->get();

        /** @var Collection<User>  */
        $employees = User::where('group', 'user')
            ->paginate(20)
            ->through(function (User $user) use ($attendances) {
                return $user->setAttribute(
                    'attendance',
                    $attendances
                        ->where(fn (Attendance $attendance) => $attendance->user_id === $user->id)
                        ->first(),
                );
            });

        $employeesCount = User::where('group', 'user')->count();
        $presentCount = $attendances->where(fn ($attendance) => $attendance->status === 'present')->count();
        $lateCount = $attendances->where(fn ($attendance) => $attendance->status === 'late')->count();
        $excusedCount = $attendances->where(fn ($attendance) => $attendance->status === 'excused')->count();
        $sickCount = $attendances->where(fn ($attendance) => $attendance->status === 'sick')->count();
        $absentCount = $employeesCount - ($presentCount + $lateCount + $excusedCount + $sickCount);

        $selectedUser = $this->selectedUserId ? User::find($this->selectedUserId) : null;

        return view('livewire.admin.dashboard', [
            'employees' => $employees,
            'employeesCount' => $employeesCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount,
            'sickCount' => $sickCount,
            'absentCount' => $absentCount,
            'selectedUser' => $selectedUser,
        ]);
    }
}
