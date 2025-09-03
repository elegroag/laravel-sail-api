<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    @php
        $path = env('APP_URL').':'.env('APP_PORT');
        $app = env('APP_NAME');
    @endphp
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
        path="{{ $path }}"
        app="{{ $app }}" />

    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/noty/noty.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/swiper/swiper.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/font_awesome/all.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/sweetalert2/dist/sweetalert2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('theme/headroom.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/nucleo.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/nucleo.svg.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/argon-mercurio.css') }}" />

    <script type="text/javascript" src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/underscore/underscore-umd-min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/backbone/backbone-min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/noty/noty.js') }}"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    @include('templates.loading')
    @yield('content')

    <script type="text/javascript" src="{{ asset('assets/plugins/js.cookie.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/jquery/jquery.scrollbar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/jquery/jquery-scrollLock.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/bootstrap/js/bootstrap-datepicker-es.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/validators/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/validators/messages_es.min.js') }}"></script>

    <script src="{{ asset('assets/argon/headroom.js') }}"></script>
    <script src="{{ asset('assets/argon/argon.js') }}"></script>

</body>

</html>
