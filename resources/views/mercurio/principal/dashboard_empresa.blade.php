@extends('layouts.dash')

@section('title', 'Estadísticas empresa')

@push('scripts')
    <script src="{{ asset('assets/chart/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/chart/Chart.extension.js') }}"></script>
@endpush

@section('content')
<div class="card-body">
    <div class="col-12">
        <div class="row justify-content-between">
            <div class="col-md-6" id='render_chart_aportes'>
                <div class="card bg-default">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-light text-uppercase ls-1 mb-1">Mensual</h6>
                                <h5 class="text-white mb-0">Aportes Pila</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-aportes" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6" id='render_chart_categorias'>
                <div class="card bg-default">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-light text-uppercase ls-1 mb-1">Categorias</h6>
                                <h5 class="text-white mb-0">Por Trabajador</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-categorias" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-md-6" id='render_chart_giro'>
                <div class="card bg-default">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-light text-uppercase ls-1 mb-1">Mensual</h6>
                                <h5 class="text-white mb-0">Cuota Monetaria</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        <div class="chart">
                            <!-- Chart wrapper -->
                            <canvas id="chart-giro" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('mercurio/build/DashBoard.js') }}"></script>
@endsection