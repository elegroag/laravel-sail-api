@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
@endpush

@push('scripts')
<script>
    window.ServerController = 'principal';
</script>

<script src="{{ asset('assets/chart/Chart.min.js') }}"></script>
<script src="{{ asset('assets/chart/Chart.extension.js') }}"></script>
<script src="{{ asset('cajas/build/DashBoard.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid m-3">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card bg-default">
                                <div class="card-header bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="text-white mb-0">Usuarios Registrados</h5>
                                            <p class="text-muted text-uppercase ls-1 mb-1">Registro</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Chart -->
                                    <div class="chart">
                                        <!-- Chart wrapper -->
                                        <canvas id="chart-usuarios" class="chart-canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card bg-default">
                                <div class="card-header bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="text-white mb-0">Mas Usada</h5>
                                            <p class="text-muted text-uppercase ls-1 mb-1">Opcion</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Chart -->
                                    <div class="chart">
                                        <!-- Chart wrapper -->
                                        <canvas id="chart-opcion" class="chart-canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card bg-default">
                                <div class="card-header bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="text-white mb-0">Mas usado</h5>
                                            <p class="text-muted text-uppercase ls-1 mb-1">Motivo Rechazo</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Chart -->
                                    <div class="chart">
                                        <!-- Chart wrapper -->
                                        <canvas id="chart-rechazo" class="chart-canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card bg-default">
                                <div class="card-header bg-transparent">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="text-white mb-0">Laboral</h5>
                                            <p class="text-muted text-uppercase ls-1 mb-1">Carga</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Chart -->
                                    <div class="chart">
                                        <!-- Chart wrapper -->
                                        <canvas id="chart-laboral" class="chart-canvas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
@endsection
