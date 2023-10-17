<x-main-layout>
    <div class="relative bg-white">
      {{-- <img class="h-56 w-full object-cover lg:absolute  lg:inset-y-0 lg:left-0 lg:h-full lg:w-1/2"
        src="{{ asset('images/darbcmap3.jpg') }}" alt=""> --}}
      <div class="grid max-w-7xl">
        <div class="px-6 pt-2 pb-2 lg:pt-2">
          <div class="max-w-2xl lg:max-w-lg">
            <h2 class="text-2xl uppercase font-montserrat font-bold leading-8 text-indigo-600">DARBC</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-700 sm:text-4xl">Event Attendance Module</p>
            {{-- <p class="mt-2 text-3xl font-bold tracking-tight text-gray-700 sm:text-lg">(Health, Death, Mortuary, LOG and CA)</p> --}}
          </div>
        </div>
      </div>
      <div class="px-6 mt-4">
          <livewire:admin.dashboard />
      </div>
    </div>

  </x-main-layout>
