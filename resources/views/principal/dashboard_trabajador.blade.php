@extends('layouts.bone')

@section('title', 'Dashboard Trabajador')

@section('content')
@csrf

<script src="{{ asset('assets/chart/Chart.min.js') }}" />
<script src="{{ asset('assets/chart/Chart.extension.js') }}" />

<div class="row">
    <div class="col-xl-8">
        <div class="card bg-default">
            <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-light text-uppercase ls-1 mb-1">Mensual</h6>
                        <h5 class="h3 text-white mb-0">Aportes Pila</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Chart -->
                <div class="chart">
                    <!-- Chart wrapper -->
                    <canvas id="chart-xx" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header bg-transparent">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
                        <h5 class="h3 mb-0">Total orders</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Chart -->
                <div class="chart">
                    <canvas id="chart-bars" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div id='boneLayout'></div>

<script src="{{ asset('mercurio/build/DashBoard.js') }}"></sc>

@endsection
