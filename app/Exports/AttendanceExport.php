<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{
    public $attendance;
    public $selected_event;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($selected_event)
    {
        $this->selected_event = $selected_event;
        $this->attendance = Attendance::when($this->selected_event, function ($query) {
            $query->where('event_id', $this->selected_event);
        })->get();
    }

    public function view(): View
    {
        return view('exports.attendance', [
            'attendance' => $this->attendance,
        ]);
    }
}
