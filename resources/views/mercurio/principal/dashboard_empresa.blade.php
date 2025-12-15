@extends('layouts.bone')

@section('title', 'Estadísticas empresa')

@push('styles')
	<style>
		.dashboard-wrap {
			padding: 1rem;
		}

		.dashboard-card {
			border: 1px solid rgba(0, 0, 0, 0.08);
			border-radius: 0.75rem;
			overflow: hidden;
			box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
		}

		.dashboard-card .card-header {
			padding: 0.9rem 1rem;
			border-bottom: 1px solid rgba(0, 0, 0, 0.06);
		}

		.dashboard-kicker {
			font-size: 0.75rem;
			letter-spacing: 0.08em;
			text-transform: uppercase;
			opacity: 0.8;
			margin-bottom: 0.15rem;
		}

		.dashboard-title {
			font-size: 1.05rem;
			font-weight: 600;
			margin: 0;
		}

		.chart {
			background: #ffffff;
			border-radius: 0.5rem;
			padding: 0.75rem;
			border: 1px solid rgba(0, 0, 0, 0.06);
			position: relative;
			min-height: clamp(220px, 28vh, 320px);
		}

		.chart-canvas {
			background: #ffffff;
			display: block;
			width: 100% !important;
			height: clamp(220px, 28vh, 320px) !important;
		}

		.chart-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			background: rgba(255, 255, 255, 0.92);
			backdrop-filter: blur(2px);
			border-radius: 0.5rem;
			text-align: center;
			padding: 1rem;
		}
	</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/chart/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/chart/Chart.extension.js') }}"></script>
	<script src="{{ asset('mercurio/build/DashBoard.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-wrap">
	<div class="container-fluid px-0">
		<div class="row g-4">
			<div class="col-12 col-lg-6" id='render_chart_aportes'>
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Mensual</div>
								<h5 class="dashboard-title">Aportes Pila</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading" aria-live="polite">
								<div>
									<div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
									<div class="mt-2 text-muted">Cargando información…</div>
								</div>
							</div>
							<div class="chart-overlay chart-empty d-none">
								<div>
									<div class="fw-semibold mb-1">Sin datos</div>
									<div class="text-muted small">No hay información disponible para mostrar.</div>
								</div>
							</div>
							<canvas id="chart-aportes" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6" id='render_chart_categorias'>
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Categorias</div>
								<h5 class="dashboard-title">Por Trabajador</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading" aria-live="polite">
								<div>
									<div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
									<div class="mt-2 text-muted">Cargando información…</div>
								</div>
							</div>
							<div class="chart-overlay chart-empty d-none">
								<div>
									<div class="fw-semibold mb-1">Sin datos</div>
									<div class="text-muted small">No hay información disponible para mostrar.</div>
								</div>
							</div>
							<canvas id="chart-categorias" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6" id='render_chart_giro'>
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Mensual</div>
								<h5 class="dashboard-title">Cuota Monetaria</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading" aria-live="polite">
								<div>
									<div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
									<div class="mt-2 text-muted">Cargando información…</div>
								</div>
							</div>
							<div class="chart-overlay chart-empty d-none">
								<div>
									<div class="fw-semibold mb-1">Sin datos</div>
									<div class="text-muted small">No hay información disponible para mostrar.</div>
								</div>
							</div>
							<canvas id="chart-giro" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection