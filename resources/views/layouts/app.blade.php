<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', config('app.name', 'Ifinance'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    {{-- Velzon Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">

    {{-- Plugin CSS --}}
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" />

    {{-- Vite Assets (nếu bạn đang build riêng) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Extra Styles --}}
    @yield('styles')

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    @stack('scripts')

</head>

<body data-layout="vertical">

    {{-- Navbar / Sidebar --}}
    @include('layouts.navigation')

    {{-- Header --}}
    @isset($header)
    <header class="bg-white border-bottom py-1 shadow-sm mb-2">
        <div class="container-fluid px-2 ">
            <h1 class="h5 m-0">{{ $header }}</h1>
        </div>
    </header>
    @endisset

    {{-- Page Content --}}
    <main class="container-fluid px-4 bg-light-subtle p-4" style="min-height: 100vh;">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Footer --}}
    {{-- <footer class="text-center text-muted py-3 small">© 2025 Ifinance</footer> --}}

    <!-- Core JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>

    <!-- Plugins JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/card/card.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/pages/widgets.init.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-input-spin.init.js') }}"></script>

    <!-- App JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    {{-- Extra Scripts --}}
    @yield('scripts')
</body>

</html>