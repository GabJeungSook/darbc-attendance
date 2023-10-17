<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public $event;
    public $total_members;
    public $total_attendance;
    public $total_absent;

    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();
        if($this->event != null)
        {
            $this->total_members = \App\Models\Members::count();
            $this->total_attendance = \App\Models\Attendance::where('event_id', $this->event->id)->count();
            $this->total_absent = $this->total_members - $this->total_attendance;
        }


    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
