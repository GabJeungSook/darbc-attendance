<div>
    @if($this->event->has_printer === 1)
    {{$this->table}}
    @else
    <div>
        <span class="inline-flex items-center gap-x-1.5 rounded-md bg-red-100 px-2 py-2 text-md font-semibold text-red-700">
            <svg class="h-1.5 w-1.5 fill-red-500" viewBox="0 0 6 6" aria-hidden="true">
              <circle cx="3" cy="3" r="3" />
            </svg>
            Printer is not required in this event
          </span>
        </div>
    @endif
 
</div>
