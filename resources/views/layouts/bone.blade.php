@extends('layouts.main')

@section('content-main')
@php
use App\Services\Menu\Menu;

$tipo = session()->get('tipo');
$user = session()->get('user');
list($menu, $migas) = Menu::showMenu();
@endphp

@include('partials.flash')

<link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />

@include('templates.sidebar', array('menu' => $menu, '_tipo' => $tipo))

<div class="main-content" id="panel">
@include('templates.navbar', array('user_name' => capitalize($user['nombre'])))
    @yield('content')
@include('templates.footer')
</div>

@include('templates.modal')

@endsection
