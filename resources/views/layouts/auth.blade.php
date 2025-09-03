<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/noty/noty.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/swiper/swiper.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/font_awesome/all.css') }}"/>

    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/noty/noty.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/swiper/swiper.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/select2/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/font_awesome/all.css') }}"/>

    <link href="{{ asset('assets/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('theme/headroom.css') }}" rel="stylesheet" />
    <link href="{{ asset('theme/nucleo.css') }}" rel="stylesheet" />
    <link href="{{ asset('theme/nucleo.svg.css') }}" rel="stylesheet" />
    <link href="{{ asset('theme/argon-mercurio.css') }}" rel="stylesheet" />

    <script src="{{ asset('assets/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/underscore/underscore-umd-min.js') }}"></script>
    <script src="{{ asset('assets/backbone/backbone-min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/noty/noty.js') }}"></script>

</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold text-center mb-4">@yield('title')</h1>
        @yield('content')
    </div>
    
    <script src="{{ asset('assets/plugins/js.cookie.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/jquery/jquery-scrollLock.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap-datepicker-es.js') }}"></script>
    <script src="{{ asset('assets/validators/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/validators/messages_es.min.js') }}"></script>
</body>
</html>
