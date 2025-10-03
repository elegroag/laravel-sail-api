@extends('layouts.main')

@section('application', 'mercurio')

@section('content-main')
@php
$user = session()->get('user');
list($menu, $breadcrumbs, $pageTitle) = App\Services\Menu\Menu::showMenu('ME');
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/css/argon-mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-sidenav.css') }}" />
    <link rel="stylesheet" href="{{ asset('theme/css/argon-content.css') }}" />
    <link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('core/messages.js') }}"></script>
    <script src="{{ asset('core/global.js') }}"></script>
    <script src="{{ asset('core/base-source.js') }}"></script>
@endpush

@include('partials.flash')

@include('templates.sidebar', 
    [
        'menu' => $menu, 
        '_tipo' => session()->get('tipo'), 
        '_estado_afiliado' => session()->get('estado_afiliado')
    ])

<div class="main-content" id="panel">
@include('templates.navbar', ['user_name' => capitalize($user['nombre']), 'breadcrumbs'=> $breadcrumbs, 'pageTitle'=> ($pageTitle)? $pageTitle : $title ]) 

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-header">
                    <h4 class="font-weight-bold">
                        @yield('title')
                    </h4>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</div>

   
@include('templates.footer')
</div>

@include('templates.modal')

@endsection
