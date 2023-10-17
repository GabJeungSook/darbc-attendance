<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Attendance extends Component
{
    public $event;

    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();
    }
    public function render()
    {
        return view('livewire.admin.attendance');
    }
}
