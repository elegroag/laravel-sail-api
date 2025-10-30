@extends('layouts.main')

@section('application', 'cajas')

@section('content-main')
@include('partials.flash')

@php
$user = session()->get('user');
list($menu, $breadcrumbs, $pageTitle) = App\Services\Menu\MenuCajas::showMenu('CA');
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/css/argon-mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-sidenav.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-content.css') }}" />
    <link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('core/messages.js') }}"></script>
@endpush

@include('templates.sidebar-cajas', 
    [
        'menu' => $menu, 
        '_tipo' => session()->get('tipo'), 
        '_estado_afiliado' => session()->get('estado_afiliado')
    ])

<div class="main-content" id="panel">
    @include('templates.navbar-cajas', [
        'user_name' => capitalize($user['nombre']), 
        'breadcrumbs'=> $breadcrumbs, 
        'pageTitle'=> $pageTitle
    ]) 
    @yield('content')
    @include('templates.footer')
</div>

@include('templates.modal')

@endsection