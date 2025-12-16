<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    @php
        $path = env('APP_URL').':'.env('APP_PORT');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
        path="{{ $path }}"
        app="@yield('application')" />
        
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/noty/noty.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font_awesome/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/sweetalert2/dist/sweetalert2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('theme/css/headroom.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/nucleo.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/nucleo.svg.css') }}" />

    <script type="text/javascript" src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/underscore/underscore-umd-min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/backbone/backbone-min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/noty/noty.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/select2/js/select2.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('theme/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/validators/jquery.validate.js') }}"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen g-sidenav-pinned">
    @include('templates.loading')

    @yield('content-main')

    <script type="text/javascript" src="{{ asset('assets/plugins/js.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/jquery/jquery.scrollbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/jquery/jquery-scrollLock.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap-datepicker-es.js') }}"></script>
    
    <script src="{{ asset('assets/validators/messages_es.min.js') }}"></script>
    <script src="{{ asset('theme/js/headroom.js') }}"></script>
    <script src="{{ asset('theme/js/navbarEvents.js') }}"></script>
    <script src="{{ asset('theme/js/sidenav.js') }}"></script>
    <script src="{{ asset('theme/js/scrollbar.js') }}"></script>
    <script src="{{ asset('theme/js/scroll-to.js') }}"></script>
    @stack('scripts')
</body>

</html>
