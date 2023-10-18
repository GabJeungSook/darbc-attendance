<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public $event;
    public $total_members;
    public $total_attendance;
    public $total_absent;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $total_attendance_by_date;
    public $total_absent_by_date;
    public $giveaways;

   public function generateCount()
   {
        if($this->date_from == null || $this->date_to == null)
        {
            $this->total_attendance_by_date = \App\Models\Attendance::where('event_id', $this->event->id)
            ->count();
        }elseif($this->time_from == null || $this->time_to == null)
        {
            $this->total_attendance_by_date = \App\Models\Attendance::where('event_id', $this->event->id)
            ->whereDate('created_at', '>=', $this->date_from)
            ->whereDate('created_at', '<=', $this->date_to)
            ->count();
        }
        else{
            $this->total_attendance_by_date = \App\Models\Attendance::where('event_id', $this->event->id)
            ->whereDate('created_at', '>=', $this->date_from)
            ->whereDate('created_at', '<=', $this->date_to)
            ->whereTime('created_at', '>=', $this->time_from)
            ->whereTime('created_at', '<=', $this->time_to)
            ->count();
        }
   }

    public function mount()
    {
        $this->event = \App\Models\Event::where('event_status', 1)->first();
        if($this->event != null)
        {
            $this->total_members = \App\Models\Members::count();
            $this->total_attendance = \App\Models\Attendance::where('event_id', $this->event->id)->count();
            $this->total_absent = $this->total_members - $this->total_attendance;
            $this->date_from = \Carbon\Carbon::parse($this->event->date_of_event)->format('Y-m-d');
            $this->date_to = \Carbon\Carbon::parse($this->event->date_of_event)->format('Y-m-d');
            $this->total_attendance_by_date = \App\Models\Attendance::where('event_id', $this->event->id)->whereDate('created_at', $this->event->created_at->format('Y-m-d'))->count();
            $this->total_absent_by_date = $this->total_members - $this->total_attendance_by_date;


            //show all giveaways and their total
            $this->giveaways = \App\Models\Giveaway::all();
            // $this->giveaways = \App\Models\Giveaway::where('attendances', function($query){
            //     $query->where('event_id', $this->event->id);
            // })->get();
            // $this->giveaways = \App\Models\Giveaway::whereHas('attendances', function($query){
            //     $query->where('event_id', $this->event->id);
            // })->get();
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
