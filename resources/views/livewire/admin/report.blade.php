<div x-data>
    @if($event != null)
    <div class="flex justify-between">
        <div>
            <div>
                {{-- <x-button label="Back" class="font-bold" icon="arrow-left" positive  wire:click="redirectToHealth" /> --}}
              </div>
          </div>
        {{-- <div class="select flex space-x-2 items-end">
            <x-native-select label="Report" wire:model="report_get">
              @foreach ($reports as $report)
              <option value={{$report->id}}>{{$report->event_name}}</option>
              @endforeach
            </x-native-select>
            <x-button.circle positive icon="refresh" spinner="report_get" />
          </div> --}}
    </div>


    <div class="mt-5 flex justify-between items-end">
        <div class="flex space-x-2">
             <div class="mt-5 flex space-x-2 ">
        <x-button label="PRINT" sm dark icon="printer" class="font-bold"
          @click="printOut($refs.printContainer.outerHTML);" />
        <x-button label="EXPORT" sm positive wire:click="exportReport"
          spinner="exportReport" icon="document-text" class="font-bold" />
      </div>

        </div>
        <div class="flex space-x-3">
            <x-input icon="search" label="Search" placeholder="DARBC ID Or Name" wire:model="search_query"/>
            <x-select label="Select Event" placeholder="Active Event" wire:model="selected_event">
                @foreach ($events as $item)
                <x-select.option label="{{$item->event_name}}" value="{{$item->id}}" />
                @endforeach
            </x-select>
            <x-datetime-picker
                label="From"
                placeholder="Date Attended"
                wire:model="from_date"
                without-time
            />
            <x-datetime-picker
            label="To"
            placeholder="Date Attended"
            wire:model="to_date"
            without-time
        />
        </div>


    </div>

  <div class="mt-5 border rounded-lg p-4" x-ref="printContainer">
    <div class="flex space-x-1">
        <div class="grid place-content-center">
          <img src="{{ asset('images/darbc.png') }}" class="h-10" alt="">
        </div>
        <div>
          <h1 class="text-xl font-bold text-gray-700"> DOLEFIL AGRARIAN REFORM BENEFICIARIES COOP.</h1>
          <h1>DARBC Complex, Brgy. Cannery Site, Polomolok, South Cotabato</h1>
        </div>
      </div>
      <h1 class="text-xl mt-5 text-center font-bold text-gray-700">{{$event->event_name}}</h1>
      <h1 class="text-lg text-center font-semibild text-gray-700">{{\Carbon\Carbon::parse($event->date_of_event)->format('F d, Y')}}</h1>
      <h1 class="text-lg text-center font-semibild text-gray-700">Attendance</h1>
      <div class="mt-5 overflow-x-auto">
        <table id="example" class="table-auto mt-5" style="width:100%">
          <thead class="font-normal">
            <tr>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">DARBC ID
              </th>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">LAST NAME
              </th>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">FIRST NAME
              </th>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">AREA
              </th>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">DATE ATTENDED
              </th>
              <th class="border text-left whitespace-nowrap px-2 text-sm font-semibold text-gray-500 py-2">TIME ATTENDED
              </th>
            </tr>
          </thead>
          <tbody class="">
            @foreach ($attendance as $item)
              <tr>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3 py-1">{{ strtoupper($item->member->darbc_id) }}</td>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->last_name) }}</td>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->first_name) }}</td>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper($item->area) }}</td>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper(\Carbon\Carbon::parse($item->created_at)->format('F d, Y')) }}</td>
                <td class="border text-gray-600 text-sm whitespace-nowrap px-3  py-1">{{ strtoupper(\Carbon\Carbon::parse($item->created_at)->format('h:i A')) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="mt-10 flex justify-around">
          {{-- @foreach ($fourth_signatories as $item)
              <div class="mt-5">
                  <h1>{{$item->description}}:</h1>
                  @if ($item->name == null || $item->name == '')
                  <div class="mt-6 w-36 h-0.5 bg-gray-600">
                  </div>
                  @else
                  <span class="font-bold">{{$item->name}}</span>
                  @endif
                  <h1 class="text-sm">{{$item->position ?? ''}}</h1>
              </div>
          @endforeach --}}

      </div>
      </div>
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
