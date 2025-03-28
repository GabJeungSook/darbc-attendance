<?php

namespace App\Http\Livewire\Admin;

use Excel;
use App\Models\Event;
use Livewire\Component;
use App\Models\Attendance;

class Report extends Component
{
    public $report_get;
    public $event;
    public $selected_event;
    public $from_date;
    public $to_date;
    public $attendance;
    public $search_query;

    public function mount()
    {
        $this->event = Event::where('event_status', true)->first();
    }

    public function updatedSelectedEvent($value)
    {
        if($value == null)
        {
            $this->event = Event::where('event_status', true)->first();
            $this->selected_event = $this->event->id;
        }else{
            $this->event = Event::find($value);
        }
    }

    public function exportReport()
    {
        return Excel::download(
            new \App\Exports\AttendanceExport($this->selected_event, $this->from_date, $this->to_date, $this->search_query),
            'attendance.xlsx');
    }

    public function render()
    {
        $this->attendance = Attendance::when($this->selected_event, function ($query) {
            $query->where('event_id', $this->selected_event);
        })
        ->when($this->search_query, function ($query) {
            $query->whereHas('member', function ($query) {
                $query->where('last_name', 'like', '%'.$this->search_query.'%')
                ->orWhere('first_name', 'like', '%'.$this->search_query.'%')
                ->orWhere('darbc_id', 'like', '%'.$this->search_query.'%');
            });


        })
        ->when($this->from_date && $this->to_date, function ($query) {
            $query->whereDate('created_at', '>=', $this->from_date)
                    ->whereDate('created_at', '<=', $this->to_date);
        })
        ->paginate(100);
        return view('livewire.admin.report', [
            'events' => Event::orderBy('event_status', 'desc')->get(),
            'attendance' => $this->attendance,
        ]);
    }
}
