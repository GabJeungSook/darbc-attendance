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
    public $from_date;
    public $to_date;
    public $search_query;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($selected_event, $from_date, $to_date, $search_query)
    {
        $this->selected_event = $selected_event;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
        $this->search_query = $search_query;

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
            $query->whereBetween('created_at', [$this->from_date, $this->to_date]);
        })
        ->get();

        // $this->attendance = Attendance::when($this->selected_event, function ($query) {
        //     $query->where('event_id', $this->selected_event);
        // })->get();
    }

    public function view(): View
    {
        return view('exports.attendance', [
            'attendance' => $this->attendance,
        ]);
    }
}
