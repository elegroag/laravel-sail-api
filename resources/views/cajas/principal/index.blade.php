@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
@endpush

@section('content')
<div class="card-body mt-0 pb-1 bt-1">
    <div class="mb-4">
        <h4 class="font-weight-bold" for='afiliaciones'>Movimientos</h4>
        <p class="text-muted">Solicitudes de afiliación</p>
        <div class="row justify-content-around mt-2 mb-2" id="show_afiliaciones">
            @foreach ($servicios as $ai => $register)
                @if ($ai == 'afiliacion')
                    @foreach ($register as $aj => $row)
                    <div class='col-xs-12 col-lg-3 mt-3'> 
                        <a href="{{ url('cajas/' . $row['url']) }}">
                            <div class="company-affiliation-card">
                                <div class="header-section"> 
                                    <img src='{{ asset('img/Mercurio/' . $row['imagen']) }}' class="img img-principal p-2" />        
                                </div>
                                <h4 class="card-title pt-3">{{ $row['name'] }}</h4>
                                @if (is_array($row['cantidad']))
                                <div class="status-grid">
                                    <div class="d-flex status-item row-align align-items-center">
                                        <div class="status-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
                                            </svg>
                                            Pendientes
                                        </div>
                                        <div class="status-value ms-auto">{{ $row['cantidad']['pendientes'] }}</div>
                                    </div>
                                    <div class="d-flex status-item row-align align-items-center">
                                        <div class="status-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.423 5.525 7.475a.235.235 0 0 0-.02-.022A.5.5 0 0 0 5.146 7l-.003.003-.004.004-.005.005a.5.5 0 0 0 .708.708l1.414 1.414 3.536-3.536a.5.5 0 0 0 .001-.707.502.502 0 0 0-.707-.001z"/>
                                            </svg>
                                            Aprobados
                                        </div>
                                        <div class="status-value ms-auto">{{ $row['cantidad']['aprobados'] }}</div>
                                    </div>
                                    <div class="d-flex status-item row-align align-items-center">
                                        <div class="status-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg>
                                            Rechazados
                                        </div>
                                        <div class="status-value ms-auto">{{ $row['cantidad']['rechazados'] }}</div>
                                    </div>
                                    <div class="d-flex status-item row-align align-items-center">
                                        <div class="status-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                                <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                            </svg>
                                            Devueltos
                                        </div>
                                        <div class="status-value ms-auto">{{ $row['cantidad']['devueltos'] }}</div>
                                    </div>
                                </div>
                                @endif
                    
                                <div class="divider"></div>
                                <div class="temporary-section">
                                    <div class="temporary-label text-muted">Temporales</div>
                                    <div class="temporary-value">{{ $row['cantidad']['temporales'] }}</div>
                                </div>
                            </div>
                        </a>      
                    </div>
                    @endforeach
                @endif
            @endforeach
        </div>
    </div>

    <div class="mt-3 mb-4">
        <div class="p-2">
            <h4 class="font-weight-bold"  for='productos'>Productos y Servicios</h4>
            <p class="text-muted">Productos y servicios adicionales de la CAJA de Compensación del Caquetá</p>
        </div>
        <div class="d-flex justify-content-start mt-4 mb-2" id="show_productos">
            @foreach ($servicios as $ai => $register)
                @if ($ai == 'productos')
                    @foreach ($register as $aj => $row)
                        <div class="p-2 box" style="cursor:pointer" data-toggle='action' data-href='{{ $row['url'] }}'>
                            <div class="card card-stats" style="min-width: 250px; min-height: 150px">
                                <div class="card-header card-header-warning card-header-icon">
                                    <p class="card-category">{{ $row['name'] }}</p>
                                    <img src='{{ asset('img/Mercurio/' . $row['imagen']) }}' class="img img-principal" />
                                </div>
                            </div>
                        </div>
                    @endforeach 
                @endif
            @endforeach
        </div>
    </div>
</div>
<script src="{{ asset('cajas/build/Inicio.js') }}"></script>
@endsection
