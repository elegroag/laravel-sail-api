@extends('layouts.main')

@section('application', 'mercurio')

@section('content-main')
@php
use App\Services\Menu\Menu;

$tipo = session()->get('tipo');
$user = session()->get('user');
list($menu, $migas) = Menu::showMenu();
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('theme/argon-mercurio.css') }}" />
    <link rel="stylesheet" href="{{ asset('mercurio/css/mercurio.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('core/messages.js') }}"></script>
    <script src="{{ asset('core/global.js') }}"></script>
    <script src="{{ asset('core/base-source.js') }}"></script>
@endpush

@include('partials.flash')

@include('templates.sidebar', array('menu' => $menu, '_tipo' => $tipo))

<div class="main-content" id="panel">
@include('templates.navbar', array('user_name' => capitalize($user['nombre'])))    

<div class="header bg-gradient-primary pb-6 navbar-dark" id='header_group_button'>
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-md-4 col-auto mr-auto">
                    <h4 class="text-white d-inline-block mb-0">{{ isset($title) ? $title : "Sin Titulo" }}</h4>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                            @php echo $migas @endphp
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid mt--6">
    <div class="row">
        <div class="col">
            <div class="card">
                @if (isset($hide_header))
                @else
                    <div class="card-header border-0">
                        <h5 class="mb-0">{{ (isset($title)) ? $title : "Sin Titulo"; }}</h5>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</div>

   
@include('templates.footer')
</div>

@include('templates.modal')

@endsection
