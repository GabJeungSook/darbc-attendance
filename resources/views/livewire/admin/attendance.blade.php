<div>
    @if($event->event_status === 1)
    <div>
         <span class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-2 text-lg font-semibold text-green-700">
            <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">
              <circle cx="3" cy="3" r="3" />
            </svg>
            {{$event->event_name}} - {{\Carbon\Carbon::parse($event->date_of_event)->format('F d, Y')}}
          </span>
          <div class="mt-4">
            {{$this->table}}
          </div>
        </div>
    {{-- @elseif($event != null && $event->has_printer === 1 && !auth()->user()->printer()->exists())
    <div>
        <span class="inline-flex items-center gap-x-1.5 rounded-md bg-red-100 px-2 py-2 text-md font-semibold text-red-700">
            <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true">
              <circle cx="3" cy="3" r="3" />
            </svg>
            No Printer Added
          </span>
        </div> --}}
    @else
    <div>
    <span class="inline-flex items-center gap-x-1.5 rounded-md bg-red-100 px-2 py-2 text-md font-semibold text-red-700">
        <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true">
          <circle cx="3" cy="3" r="3" />
        </svg>
        No Active Event
      </span>
    </div>
    @endif
</div>
