@extends('layouts.main')

@section('title', 'Iniciar Sesión')

@section('content')
@php
use App\Services\Menu\Menu;

$user = session()->all();
list($menu, $migas) = Menu::showMenu();
@endphp

@include('partials.flash')

<link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />

<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />

{{ View::renderView("layout/tmp-bone/sidebar", array('menu' => $menu, '_tipo' => $user['tipo'])) }}

<div class="main-content" id="panel">
    {{ View::render("layout/tmp-bone/navbar", array('user_name' => Tag::capitalize($user['nombre']))); }}
    @yield('content')
    {{ View::renderView("layout/tmp-bone/footer") }}
</div>

{{ View::renderView("layout/tmp-bone/modal") }}

@endsection
