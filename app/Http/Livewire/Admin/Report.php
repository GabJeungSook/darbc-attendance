<?php

namespace App\Http\Livewire\Admin;

use App\Models\Event;
use Livewire\Component;
use App\Models\Attendance;

class Report extends Component
{
    public $report_get;
    public $event;
    public $selected_event;
    public $attendance;

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

    public function render()
    {
        $this->attendance = Attendance::when($this->selected_event, function ($query) {
            $query->where('event_id', $this->selected_event);
        })->get();
        return view('livewire.admin.report', [
            'events' => Event::orderBy('event_status', 'desc')->get(),
            'attendance' => $this->attendance,
        ]);
    }
}
