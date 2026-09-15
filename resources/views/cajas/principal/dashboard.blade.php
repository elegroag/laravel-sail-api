@extends('layouts.cajas')

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
		min-height: 35vh;
	}

	.chart-canvas {
		background: #ffffff;
		display: block;
		width: 100% !important;
		height: 35vh !important;
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
<script>
	window.ServerController = 'principal';
</script>
<script src="{{ asset('assets/chart/Chart.min.js') }}"></script>
<script src="{{ asset('assets/chart/Chart.extension.js') }}"></script>
<script src="{{ versioned_asset('cajas/build/DashBoard.js') }}"></script>
@endpush

@section('content')
<div class="dashboard-wrap">
	<div class="container-fluid px-0">
		<div class="row g-4">
			<div class="col-12 col-lg-6">
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Registro</div>
								<h5 class="dashboard-title">Usuarios Registrados</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading d-none" aria-live="polite">
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
							<canvas id="chart-usuarios" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6">
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Opción</div>
								<h5 class="dashboard-title">Más Usada</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading d-none" aria-live="polite">
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
							<canvas id="chart-opcion" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6">
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Motivo Rechazo</div>
								<h5 class="dashboard-title">Más Usado</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading d-none" aria-live="polite">
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
							<canvas id="chart-rechazo" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-6">
				<div class="card dashboard-card bg-white h-100">
					<div class="card-header bg-white">
						<div class="d-flex justify-content-between align-items-start">
							<div>
								<div class="dashboard-kicker text-muted">Carga</div>
								<h5 class="dashboard-title">Laboral</h5>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart">
							<div class="chart-overlay chart-loading d-none" aria-live="polite">
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
							<canvas id="chart-laboral" class="chart-canvas"></canvas>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
