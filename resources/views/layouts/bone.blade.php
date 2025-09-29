@extends('layouts.main')

@section('application', 'mercurio')

@section('content-main')
@php
$tipo = session()->get('tipo');
$user = session()->get('user');
list($menu, $migas) = App\Services\Menu\Menu::showMenu();
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/argon-mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />
@endpush

@include('partials.flash')

@include('templates.sidebar', array('menu' => $menu, '_tipo' => $tipo))

<div class="main-content" id="panel">
@include('templates.navbar', array('user_name' => capitalize($user['nombre'])))    
    @yield('content')
@include('templates.footer')
</div>

@include('templates.modal')

@endsection
