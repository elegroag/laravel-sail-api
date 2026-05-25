@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('cajas/build/Inicio.js') }}"></script>
@endpush

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="mb-4 mt-5">
            <h4 class="font-weight-bold" for='afiliaciones'>Movimientos</h4>
            <p class="text-muted">Solicitudes de afiliación</p>
            <div class="row justify-content-around mt-2 mb-2" id="show_afiliaciones">
                @foreach ($servicios as $ai => $register)
                @if ($ai == 'afiliacion')
                @foreach ($register as $aj => $row)
                <div class='col-xs-12 col-lg-3 mt-3'>
                    <a href="{{ url('cajas/' . $row['url']) }}">
                        <div class="company-affiliation-card">
                            <div class="header-section justify-content-center">
                                <img src='{{ asset('img/Mercurio/' . $row['imagen']) }}' class="img img-principal p-2" />
                            </div>
                            <h4 class="card-title pt-3">{{ $row['name'] }}</h4>
                            @if (is_array($row['cantidad']))
                            <div class="status-grid">
                                <div class="d-flex justify-content-between status-item row-align align-items-center">
                                    <div class="status-label">
                                        Pendientes
                                    </div>
                                    <div class="status-value ms-auto text-muted">{{ $row['cantidad']['pendientes'] }}</div>
                                </div>
                                <div class="d-flex justify-content-between status-item row-align align-items-center">
                                    <div class="status-label">
                                        Aprobados
                                    </div>
                                    <div class="status-value ms-auto text-muted">{{ $row['cantidad']['aprobados'] }}</div>
                                </div>
                                <div class="d-flex justify-content-between status-item row-align align-items-center">
                                    <div class="status-label">
                                        Rechazados
                                    </div>
                                    <div class="status-value ms-auto text-muted">{{ $row['cantidad']['rechazados'] }}</div>
                                </div>
                                <div class="d-flex justify-content-between status-item row-align align-items-center">
                                    <div class="status-label">
                                        Devueltos
                                    </div>
                                    <div class="status-value ms-auto text-muted">{{ $row['cantidad']['devueltos'] }}</div>
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
                <h4 class="font-weight-bold" for='productos'>Productos y Servicios</h4>
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
</div>
@endsection