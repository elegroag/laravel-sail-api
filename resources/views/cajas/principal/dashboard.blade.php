<div class="card-body">
    <div class="row">
        <div class="col-xl-6">
            <div class="card bg-default">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-light text-uppercase ls-1 mb-1">Registro</h6>
                            <h5 class="h3 text-white mb-0">Usuarios Registrados</h5>
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
                            <h6 class="text-light text-uppercase ls-1 mb-1">Opcion</h6>
                            <h5 class="h3 text-white mb-0">Mas Usada</h5>
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
                            <h6 class="text-light text-uppercase ls-1 mb-1">Motivo Rechazo</h6>
                            <h5 class="h3 text-white mb-0">Mas usado</h5>
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
                            <h6 class="text-light text-uppercase ls-1 mb-1">Carga</h6>
                            <h5 class="h3 text-white mb-0">Laboral</h5>
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

<script src="{{ asset('Cajas/dashboard/build.dashboard.js') }}"></script>
<script src="{{ asset('chart/Chart.min.js') }}"></script>
<script src="{{ asset('chart/Chart.extension.js') }}"></script>
