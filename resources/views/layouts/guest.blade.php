<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Montserrat:wght@400;500;600;700;800;900&display=swap"
    rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <script src=" https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js "></script>
  <script src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>
  <!-- Scripts -->
  @wireUiScripts
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @livewireStyles
</head>

<body class="font-sans text-gray-900 antialiased">
  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    {{-- <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div> --}}

    <div class="w-full sm:max-w-md mt-6 px-6 py-5 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div class="flex justify-center items-center flex-col space-y-2">
        <img src="{{ asset('images/darbc.png') }}" class="h-14" alt="">
        <div class="flex justify-center text-center flex-col">
          <h1 class="text-2xl font-bold font-montserrat text-sidebar ">Welcome to DARBC</h1>
          <p class="text-gray-700">Enter a valid account to continue signing in.</p>
        </div>
      </div>
      <div class="mt-5">
        {{ $slot }}
      </div>
    </div>
  </div>
</body>
@livewireScripts

</html>
