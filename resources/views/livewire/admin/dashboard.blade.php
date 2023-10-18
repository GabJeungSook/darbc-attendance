<div>
    @if($event != null)
    <div wire:poll.visible>
        <div class="flex justify-between">
                <div>
                    <span class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2 py-2 text-lg font-semibold text-green-700">
                        <svg class="h-1.5 w-1.5 fill-green-500" viewBox="0 0 6 6" aria-hidden="true">
                        <circle cx="3" cy="3" r="3" />
                        </svg>
                        {{$event->event_name}} - {{\Carbon\Carbon::parse($event->date_of_event)->format('F d, Y')}}
                    </span>
                </div>
            <div>
                <x-button warning icon="printer" label="Test Printer" wire:click="testPrinter"/>
            </div>
        </div>

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
      @if(auth()->user()->role_id == 1)
      <div class="bg-gray-200 p-3 rounded-md mt-4">
        <div class="text-lg font-semibold tracking-wide mb-3">
            <h1>Daily Registration Count</h1>
        </div>
        <div class="grid grid-cols-4 space-y-2">
            <div class="col-start-1 mt-2 flex space-x-3">
                <x-datetime-picker label="Date From" placeholder="From" min="{{$date_from}}" class="w-full" without-time wire:model.defer="date_from"/>
                <x-datetime-picker label="Date To" placeholder="To" min="{{$date_from}}" class="w-full" without-time wire:model.defer="date_to"/>
            </div>
            <div class="col-start-2 flex space-x-3">
                <x-time-picker label="AM/PM" placeholder="12:00 AM" wire:model.defer="time_from"/>
                <x-time-picker label="AM/PM" placeholder="12:00 AM" wire:model.defer="time_to"/>
            </div>
            <div class="flex items-end p-2">
                <div class="py-4">
                    <x-button emerald label="Generate" wire:click="generateCount"/>
                </div>
            </div>
        </div>
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <dt class="truncate text-sm font-medium text-gray-500">Total Members</dt>
              <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_members}}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <dt class="truncate text-sm font-medium text-gray-500">Total Members Attended</dt>
              <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_attendance_by_date}}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
              <dt class="truncate text-sm font-medium text-gray-500">Total Members Not Attended</dt>
              <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{$total_absent_by_date}}</dd>
            </div>
          </dl>
      </div>

     {{-- table --}}
     <div class="px-4 sm:px-6 lg:px-4 mt-3">
        <div class="sm:flex sm:items-center">
            <div class="text-lg font-semibold tracking-wide">
                <h1>Total Giveaways</h1>
            </div>
        </div>
        <div class="mt-3 flow-root">
          <div class="-mx-2 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">

                    @foreach ($giveaways as $item)
                    <tr>
                    <td class="whitespace-nowrap px-3 py-4 text-md font-semibold text-gray-600">{{$item->name}}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-md font-semibold text-gray-600">{{\App\Models\Attendance::where('event_id', $this->event->id)->where('giveaway_id', $item->id)->count()}}</td>
                    </tr>
                    @endforeach

                      {{-- <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Lindsay Walton</td> --}}



                    <!-- More people... -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      @endif
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
