@extends('layouts.main')

@section('application', 'cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/css/argon-mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-sidenav.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-content.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('summernote/summernote-bs5.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('datatables.net/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('datatables.net.bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('summernote/summernote-bs5.js') }}"></script>

    <script src="{{ asset('summernote/lang/summernote-es-ES.js') }}"></script>

    <script id='tmp_list_header' type="text/template">
        @include('cajas/templates/tmp_list_header')
    </script>

    <script type="text/template" id='tmp_layout'>
        @include('cajas/templates/tmp_layout')
    </script>

    <script type="text/template" id='tmp_header'>
        @include('cajas/templates/tmp_header')
    </script>

    <script type="text/template" id='tmp_rechazar'>
        @include('cajas/templates/tmp_rechazar')
    </script>

    <script type="text/template" id='tmp_devolver'>
        @include('cajas/templates/tmp_devolver')
    </script>

    <script type="text/template" id='tmp_info'>
        @include('cajas/templates/tmp_information')
    </script>

    <script type="text/template" id='tmp_deshacer'>
        @include('cajas/templates/tmp_deshacer')
    </script>

    <script type="text/template" id="tmp_reaprobar">
        @include('cajas/templates/tmp_reaprobar')
    </script>

    <script id='tmp_info_header' type="text/template">
        @include('cajas/templates/tmp_info_header')
    </script>
@endpush

@section('content-main')
    @include('partials.flash')

    @php
        $user = session()->get('user');
        list($menu, $breadcrumbs, $pageTitle) = App\Services\Menu\MenuCajas::showMenu('CA');
    @endphp

    @include('templates.sidebar-cajas',
        [
            'menu' => $menu,
            '_tipo' => session()->get('tipo'),
            '_estado_afiliado' => session()->get('estado_afiliado')
        ])

    <div class="main-content" id="panel">
        @include('templates.navbar', [
            'user_name' => capitalize($user['nombre']),
            'breadcrumbs'=> $breadcrumbs,
            'pageTitle'=> $pageTitle
        ])
        <div id='boneLayout'></div>
        @include('templates.footer')
    </div>
    @include('templates.modal')
@endsection
