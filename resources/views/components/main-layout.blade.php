<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-100">

<head>
    <style>
    @media print {
        /* Hide the header and footer */
        header,
        footer {
          display: none !important;
        }
      }
      </style>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" href="{{ asset('images/darbc.png') }}" />

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap"
    rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js "></script>
  <script src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/index.global.min.js'></script>
  {{--  --}}
  <!-- Scripts -->
  @wireUiScripts
  {{-- @wireUiScripts --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles
  @stack('scripts')
</head>

<body class="antialiased">
  <!--
  This example requires some changes to your config:

  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
  <!--
  This example requires updating your template:

  ```
  <html class="h-full bg-gray-100">
  <body class="h-full">
  ```
-->
  <div>
    <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
    <div class="relative z-40 lg:hidden" role="dialog" aria-modal="true">
      <!--
      Off-canvas menu backdrop, show/hide based on off-canvas menu state.

      Entering: "transition-opacity ease-linear duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "transition-opacity ease-linear duration-300"
        From: "opacity-100"
        To: "opacity-0"
    -->
      <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>

      <div class="fixed inset-0 z-40 flex">
        <!--
        Off-canvas menu, show/hide based on off-canvas menu state.

        Entering: "transition ease-in-out duration-300 transform"
          From: "-translate-x-full"
          To: "translate-x-0"
        Leaving: "transition ease-in-out duration-300 transform"
          From: "translate-x-0"
          To: "-translate-x-full"
      -->
        <div class="relative flex w-full max-w-xs flex-1 flex-col bg-gray-800 pt-5 pb-4">
          <!--
          Close button, show/hide based on off-canvas menu state.

          Entering: "ease-in-out duration-300"
            From: "opacity-0"
            To: "opacity-100"
          Leaving: "ease-in-out duration-300"
            From: "opacity-100"
            To: "opacity-0"
        -->
          <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button type="button"
              class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
              <span class="sr-only">Close sidebar</span>
              <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="flex flex-shrink-0 items-center px-4">
            <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500"
              alt="Your Company">
          </div>
          <div class="mt-5 h-0 flex-1 overflow-y-auto">
            <nav class="space-y-1 px-2">
              <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
              <a href="#"
                class="bg-gray-900 text-white group flex items-center rounded-md px-2 py-2 text-base font-medium">
                <!-- Current: "text-gray-300", Default: "text-gray-400 group-hover:text-gray-300" -->
                <svg class="text-gray-300 mr-4 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                  stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Dashboard
              </a>
            </nav>
          </div>
        </div>

        <div class="w-14 flex-shrink-0" aria-hidden="true">
          <!-- Dummy element to force sidebar to shrink to fit close icon -->
        </div>
      </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div class="flex min-h-0 flex-1 flex-col bg-sidebar">
        <div class="flex h-16 space-x-2 flex-shrink-0 items-center bg-white px-4">
          <img class="h-8 w-auto" src="{{ asset('images/darbc.png') }}" alt="Your Company">
          <div>
            <h1 class="font-black text-sidebar font-montserrat  text-xl">DARBC</h1>
            <h1 class="font-semibold leading-3 text-indigo-600 text-sm">Event Attendance Module</h1>
          </div>
        </div>
        <div class="flex font-sans flex-1 flex-col overflow-y-auto">
          <nav class="flex-1 space-y-1 px-2 py-4">
            <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
            <a href="{{ route('dashboard') }}"
              class="{{ request()->routeIs('dashboard') ? 'bg-violet-800' : '' }} hover:bg-violet-800 text-white group flex items-center rounded-md px-2 py-2 text-sm font-medium">
              <svg class="mr-3 h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                fill="currentColor">
                <path d="M0 0h24v24H0V0z" fill="none"></path>
                <path
                  d="M4 13h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zm0 8h6c.55 0 1-.45 1-1v-4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1zm10 0h6c.55 0 1-.45 1-1v-8c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1zM13 4v4c0 .55.45 1 1 1h6c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1h-6c-.55 0-1 .45-1 1z">
                </path>
              </svg>
              Dashboard
            </a>
            <a href="{{ route('attendance') }}"
              class="{{ request()->routeIs('attendance') ? 'bg-violet-800' : '' }} hover:bg-violet-800 text-white group flex items-center rounded-md px-2 py-2 text-sm font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-3 h-6 w-6 flex-shrink-0">
                <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z" clip-rule="evenodd" />
                <path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z" clip-rule="evenodd" />
              </svg>
              Attendance
            </a>
            <a href="{{ route('event') }}"
            class="{{ request()->routeIs('event') ? 'bg-violet-800' : '' }} hover:bg-violet-800 text-white group flex items-center rounded-md px-2 py-2 text-sm font-medium">
            <svg class="mr-3 h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg"
              xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 36 36"
              preserveAspectRatio="xMidYMid meet" fill="currentColor">
              <title>list-solid-badged</title>
              <path
                d="M23.66,10H15V8h7.78a7.42,7.42,0,0,1,.89-6H8A2,2,0,0,0,6,4V32a2,2,0,0,0,2,2H28a2,2,0,0,0,2-2V13.5A7.49,7.49,0,0,1,23.66,10ZM13,26H11V24h2Zm0-4H11V20h2Zm0-4H11V16h2Zm0-4H11V12h2Zm0-4H11V8h2ZM25,26H15V24H25Zm0-4H15V20H25Zm0-4H15V16H25Zm0-4H15V12H25Z"
                class="clr-i-solid--badged clr-i-solid-path-1--badged"></path>
              <circle cx="30" cy="6" r="5"
                class="clr-i-solid--badged clr-i-solid-path-2--badged clr-i-badge"></circle>
              <rect x="0" y="0" width="36" height="36" fill-opacity="0"></rect>
            </svg>
            Events
          </a>
          <a href="{{ route('members') }}"
          class="{{ request()->routeIs('members') ? 'bg-violet-800' : '' }} hover:bg-violet-800 text-white group flex items-center rounded-md px-2 py-2 text-sm font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-3 h-6 w-6 flex-shrink-0">
                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z" clip-rule="evenodd" />
                <path d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z" />
              </svg>

          Members
          </a>
          <a href="{{ route('report') }}"
          class="{{ request()->routeIs('report') ? 'bg-violet-800' : '' }} hover:bg-violet-800 text-white group flex items-center rounded-md px-2 py-2 text-sm font-medium">
          <svg class="mr-3 h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
            <path fill-rule="evenodd" d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 003 3h.27l-.155 1.705A1.875 1.875 0 007.232 22.5h9.536a1.875 1.875 0 001.867-2.045l-.155-1.705h.27a3 3 0 003-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0018 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM16.5 6.205v-2.83A.375.375 0 0016.125 3h-8.25a.375.375 0 00-.375.375v2.83a49.353 49.353 0 019 0zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 01-.374.409H7.232a.375.375 0 01-.374-.409l.526-5.784a.373.373 0 01.333-.337 41.741 41.741 0 018.566 0zm.967-3.97a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75H18a.75.75 0 01-.75-.75V10.5zM15 9.75a.75.75 0 00-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 00.75-.75V10.5a.75.75 0 00-.75-.75H15z" clip-rule="evenodd" />
          </svg>
          Reports
          </a>
          </nav>
        </div>
      </div>
    </div>
    <div class="flex flex-col lg:pl-64">
      <div class="sticky top-0 z-10 flex h-16 flex-shrink-0 shadow-sm bg-white">
        <button type="button"
          class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden">
          <span class="sr-only">Open sidebar</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
          </svg>
        </button>
        <div class="flex flex-1 justify-between px-4">
          <div class="flex flex-1">

          </div>
          <div class="ml-4 flex items-center lg:ml-6" x-data="{ dropdown: false }">

            <div class="relative inline-block px-3 text-left">
              <div>
                <button x-on:click="dropdown = !dropdown"
                  class="group w-full rounded-md  px-3.5 py-2 text-left text-sm font-medium text-gray-700 "
                  id="options-menu-button" aria-expanded="false" aria-haspopup="true">
                  <span class="flex w-full space-x-3 items-center justify-between">
                    <span class="flex min-w-0 items-center justify-between space-x-3">
                      {{-- <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300"
                        src="https://images.unsplash.com/photo-1502685104226-ee32379fefbe?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=3&w=256&h=256&q=80"
                        alt=""> --}}
                      <span class="flex min-w-0 flex-1 flex-col">
                        <span class="truncate text-sm font-medium text-gray-700 uppercase">{{strtoupper(auth()->user()->name)}}</span>
                        <span class="truncate text-sm text-indigo-500"></span>
                      </span>
                    </span>
                    <svg class="h-5 w-5 group-hover:text-indigo-700 flex-shrink-0 text-gray-400 " viewBox="0 0 20 20"
                      fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd"
                        d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                        clip-rule="evenodd" />
                    </svg>
                  </span>
                </button>
              </div>

              <!--
          Dropdown menu, show/hide based on menu state.

          Entering: "transition ease-out duration-100"
            From: "transform opacity-0 scale-95"
            To: "transform opacity-100 scale-100"
          Leaving: "transition ease-in duration-75"
            From: "transform opacity-100 scale-100"
            To: "transform opacity-0 scale-95"
        -->
              <div x-show="dropdown" x-transition-duration="100" x-cloak
                x-transition-enter="transition ease-out duration-100"
                x-transition-enter-start="transform opacity-0 scale-95"
                x-transition-enter-end="transform opacity-100 scale-100"
                x-transition-leave="transition ease-in duration-75"
                x-transition-leave-start="transform opacity-100 scale-100"
                x-transition-leave-end="transform opacity-0 scale-95" x-on:click.away="dropdown = false"
                class="absolute right-0 left-0 z-10 mx-3 mt-1 origin-top divide-y divide-gray-200 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                role="menu" aria-orientation="vertical" aria-labelledby="options-menu-button" tabindex="-1">
                {{-- <div class="py-1" role="none">
                  <!-- Active: "bg-gray-100 text-gray-900", Not Active: "text-gray-700" -->
                  <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="options-menu-item-0">View profile</a>
                  <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="options-menu-item-1">Settings</a>
                  <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="options-menu-item-2">Notifications</a>
                </div>
                <div class="py-1" role="none">
                  <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="options-menu-item-3">Get desktop app</a>
                  <a href="#" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                    id="options-menu-item-4">Support</a>
                </div> --}}
                <div class="py-1" role="none">
                  <form method="POST" action="{{ route('logout') }}" class="flex space-x-2">
                    @csrf
                    <a href="{{ route('logout') }}"
                      onclick="event.preventDefault();
              this.closest('form').submit();"
                      class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1"
                      id="options-menu-item-5">Logout</a>

                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-700 uppercase">@yield('title')</h1>
          </div>
          <div class="mx-auto  px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-4 mt-5 rounded-lg">
              {{ $slot }}
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <x-dialog z-index="z-50" blur="md" align="center" />
  @livewireScripts
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @yield('scripts')
  @livewire('notifications')
</body>

</html>
