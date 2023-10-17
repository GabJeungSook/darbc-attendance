<div>
    @if($event != null)
    <div>
        <span class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-2 text-lg font-semibold text-green-700">
            <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">
              <circle cx="3" cy="3" r="3" />
            </svg>
            {{$event->event_name}} - {{\Carbon\Carbon::parse($event->date_of_event)->format('F d, Y')}}
          </span>
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
          <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Members</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_members}}</dd>
          </div>
          <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Members Attended</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_attendance}}</dd>
          </div>
          <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">Total Members Not Attended</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_absent}}</dd>
          </div>
        </dl>
      </div>
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
