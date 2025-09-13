@extends('layouts.main')

@section('application', 'cajas')

@section('content-main')
@php
use App\Services\Menu\Menu;

$tipo = session()->get('tipo');
$user = session()->get('user');
list($menu, $migas) = Menu::showMenu();
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/argon-cajas.css') }}" />
    <link rel="stylesheet" href="{{ asset('cajas/css/cajas.css') }}" />
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
