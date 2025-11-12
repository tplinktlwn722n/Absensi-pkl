<?php

namespace App\Livewire;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Carbon;

class ScanComponent extends Component
{
    public ?Attendance $attendance = null;
    public ?array $currentLiveCoords = null;
    public bool $showModal = false;
    public $photoData = null;

    public function showAttendanceModal()
    {
        if (is_null($this->currentLiveCoords)) {
            $this->dispatch('error', message: __('Invalid location'));
            return;
        }

        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->photoData = null;
        $this->dispatch('modalClosed');
    }

    public function capturePhoto()
    {
        // This will be triggered from JavaScript after photo is captured
    }

    public function submitAttendance()
    {
        if (is_null($this->currentLiveCoords)) {
            $this->closeModal();
            return;
        }

        if (is_null($this->photoData)) {
            $this->closeModal();
            return;
        }

        // Check if already attended today
        $existingAttendance = Attendance::where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))
            ->first();

        if ($existingAttendance) {
            $this->closeModal();
            $this->dispatch('error', message: __('You have already checked in today'));
            return;
        }

        // Save photo
        $photoPath = $this->savePhoto($this->photoData);

        // Create attendance
        $attendance = $this->createAttendance($photoPath);

        if ($attendance) {
            $this->setAttendance($attendance->fresh());
            Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
            $this->closeModal();
            $this->dispatch('success', message: __('Attendance Successful'));
        }
    }

    protected function savePhoto($photoData)
    {
        // Remove data:image/jpeg;base64, prefix
        $image = str_replace('data:image/jpeg;base64,', '', $photoData);
        $image = str_replace(' ', '+', $image);
        $imageName = 'attendance_' . Auth::user()->id . '_' . now()->format('Ymd_His') . '.jpg';
        
        Storage::disk('public')->put('attendance_photos/' . $imageName, base64_decode($image));
        
        return 'attendance_photos/' . $imageName;
    }

    protected function createAttendance($photoPath)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $timeIn = $now->format('H:i:s');
        
        // Determine status based on time (assuming work starts at 08:00)
        $status = Carbon::now()->setTimeFromTimeString('08:00:00')->lt($now) ? 'late' : 'present';
        
        return Attendance::create([
            'user_id' => Auth::user()->id,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => null,
            'shift_id' => null,
            'latitude' => doubleval($this->currentLiveCoords[0]),
            'longitude' => doubleval($this->currentLiveCoords[1]),
            'status' => $status,
            'note' => null,
            'attachment' => null,
            'photo' => $photoPath,
        ]);
    }

    protected function setAttendance(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function mount()
    {
        /** @var Attendance */
        $attendance = Attendance::where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))->first();
        
        if ($attendance) {
            $this->setAttendance($attendance);
        }
    }

    public function render()
    {
        return view('livewire.scan');
    }
}
