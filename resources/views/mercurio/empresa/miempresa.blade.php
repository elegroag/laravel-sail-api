@extends('layouts.bone')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-12 mt-4">
			<div class="row justify-content-between g-3">
				<div class="col-md-8">
					<div class="card mb-3 border-primary shadow-sm h-100">
						<div class="card-header bg-gradient-primary text-white d-flex align-items-center p-3">
							<div class="me-2 rounded-circle bg-opacity-25 p-2 d-flex align-items-center justify-content-center text-white">
								<i class="fas fa-building"></i>
							</div>
							<h5 class="mb-0 fw-bolder text-white">Estado actual de la empresa</h5>
						</div>
						<div class="card-body">
							@if(isset($empresa) && is_array($empresa))
								<div class="row g-3">
									<div class="col-md-4">
										<div class="estado-metric-card">
											<div class="estado-metric-icon bg-success bg-opacity-10 text-success">
												<i class="fas fa-circle text-white"></i>
											</div>
											<div class="estado-metric-content">
												<div class="estado-metric-label">Estado actual</div>
												<div class="estado-metric-value">
													<span class="badge {{ (isset($empresa['estado']) && $empresa['estado'] === 'I') ? 'bg-warning text-dark' : 'bg-success' }}">
														{{ isset($empresa['estado']) ? htmlspecialchars($parametros['estado'][$empresa['estado']]) : 'N/A' }}
													</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-8">
										<div class="estado-metric-card">
											<div class="estado-metric-icon bg-primary bg-opacity-10 text-primary">
												<i class="fas fa-id-card-alt text-white"></i>
											</div>
											<div class="estado-metric-content">
												<div class="estado-metric-label">Razón social</div>
												<div class="estado-metric-value text-truncate">
													{{ isset($empresa['razsoc']) ? htmlspecialchars($empresa['razsoc']) : 'N/A' }}
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="estado-metric-card">
											<div class="estado-metric-icon bg-info bg-opacity-10 text-info">
												<i class="fas fa-store text-white"></i>
											</div>
											<div class="estado-metric-content">
												<div class="estado-metric-label">Sucursales activas</div>
												<div class="estado-metric-value">
													{{ isset($sucursales) && is_array($sucursales) ? count($sucursales) : 'N/A' }}
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										@php
											$ultima_aprobacion = 'N/A';
											if (isset($trayectorias) && is_array($trayectorias) && count($trayectorias) > 0) {
												$ultima_trayectoria = end($trayectorias);
												$ultima_aprobacion = $ultima_trayectoria['fecafi'] ?? 'N/A';
											}
										@endphp
										<div class="estado-metric-card">
											<div class="estado-metric-icon bg-warning bg-opacity-10 text-warning">
												<i class="fas fa-calendar-check text-white"></i>
											</div>
											<div class="estado-metric-content">
												<div class="estado-metric-label">Última aprobación de trayectoria</div>
												<div class="estado-metric-value">
													{{ htmlspecialchars($ultima_aprobacion) }}
												</div>
											</div>
										</div>
									</div>
								</div>
							@else
								<div class="alert alert-warning mb-0">No se encontraron datos de la empresa para mostrar el resumen.</div>
							@endif
						</div>
					</div>
				</div>
				
				<div class="col-md-4">
					<div class="card mb-3 border-primary shadow-sm h-100">
						<div class="card-header bg-white border-0 d-flex align-items-center">
							<div class="me-2 rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center">
								<i class="fas fa-file-signature text-white"></i>
							</div>
							<h6 class="mb-0 fw-bold">Afiliación de empresa</h6>
						</div>
						<div class="card-body text-center">
							<p class="text-muted mb-3">Gestiona la afiliación de tu empresa</p>
							<a href="{{ route('empresa.index') }}" class="img-link d-inline-flex flex-column align-items-center text-decoration-none">
								<img src="{{ asset('img/Mercurio/empresas.jpg') }}" class="img img-center mb-2" width="130px" alt="Afiliación de empresa">
								<p class="text-muted text-center mb-0">Afiliación de empresa aquí</p>
							</a>
						</div>
					</div>
				</div>
				
				<div class="col-12">
					@if(isset($empresa['estado']) && $empresa['estado'] === 'I')
						<a href="{{ route('empresa.index') }}" class="img-link text-decoration-none">
							<div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
								<i class="fas fa-exclamation-triangle fa-2x me-3"></i>
								<span>Realiza la solicitud de activación de la empresa para que pueda hacer afiliación de trabajadores y beneficiarios.</span>
							</div>
						</a>
					@endif
				</div>
				
				<div class="col-12">
					<div id="app">
						<!-- Sección Datos de la Empresa -->
						<div class="section-title d-flex align-items-center">
							<div class="d-flex align-items-center">
								<i class="fas fa-building me-2 text-primary"></i>
								<h5 class="mb-0 fw-bolder">Datos de la Empresa</h5>
							</div>
						</div>
						<div class="card shadow-sm border-0 mb-3">
							<div class="card-body">
								<div class="data-grid mb-0">
									<div class="row justify-content-between g-3">
										@if(isset($empresa) && is_array($empresa))
											@php
												$properties = [
													'NIT' => 'nit',
													'Dígito verificación' => 'digver',
													'Tipo persona' => 'tipper',
													'Tipo documento' => 'coddoc',
													'Razón social' => 'razsoc',
													'Primer apellido' => 'priape',
													'Segundo apellido' => 'segape',
													'Primer nombre' => 'prinom',
													'Segundo nombre' => 'segnom',
													'Empresa nombre comercial' => 'nomemp',
													'Sigla' => 'sigla',
													'Dirección' => 'direccion',
													'Código ciudad' => 'codciu',
													'Cédula representante' => 'cedrep',
													'Representante legal' => 'repleg',
													'Jefe personal' => 'jefper',
													'Cédula propietario' => 'cedpro',
													'Nombre propietario' => 'nompro',
													'Email' => 'email',
													'Teléfono' => 'telefono',
													'Fax' => 'fax',
													'Código zona' => 'codzon',
													'Oficina afiliación' => 'ofiafi',
													'Calificación sucursal' => 'calsuc',
													'Dirección principal' => 'dirpri',
													'Teléfono principal' => 'telpri',
													'Ciudad principal' => 'ciupri',
													'Código asesor' => 'codase',
													'Calificación empresa' => 'calemp',
													'Tipo empresa' => 'tipemp',
													'Tipo sociedad' => 'tipsoc',
													'Tipo aportante' => 'tipapo',
													'Forma presentación' => 'forpre',
													'PYMES' => 'pymes',
													'Contratista' => 'contratista',
													'Código actividad' => 'codact',
													'Indice de aportes' => 'codind',
													'Matrícula mercantil' => 'matmer',
													'Fecha certificado' => 'feccer',
													'Trabajadores aportantes' => 'traapo',
													'Fecha aprobación' => 'fecapr',
													'Valor aporte' => 'valapo',
													'Actividad aprobada' => 'actapr',
													'Fecha afiliación' => 'fecafi',
													'Fecha sistema' => 'fecsis',
													'Fecha cambio' => 'feccam',
													'Estado' => 'estado',
													'Resultado estado' => 'resest',
													'Código estado' => 'codest',
													'Fecha estado' => 'fecest',
													'Total trabajadores' => 'tottra',
													'Total aportes' => 'totapo',
													'Tiempo transcurrido' => 'tietra',
													'Fecha mercantil' => 'fecmer',
													'Tipo duración' => 'tipdur',
													'Fecha corte' => 'feccor',
													'Teléfono trabajo' => 'telt',
													'Teléfono residencia' => 'telr',
													'Email residencia' => 'mailr',
													'Email trabajo' => 'mailt',
													'Total tratamientos' => 'tratot',
													'Observación' => 'observacion',
													'Empresa pagadora de pensiones' => 'pagadora',
													'Código documento rep. legal' => 'coddocrepleg',
													'Primer apellido rep. legal' => 'priaperepleg',
													'Segundo apellido rep. legal' => 'segaperepleg',
													'Primer nombre rep. legal' => 'prinomrepleg',
													'Segundo nombre rep. legal' => 'segnomrepleg',
												];
											@endphp
											@foreach($properties as $label => $key)
												<div class="col-sm-6 col-md-4 col-lg-3">
													<div class='box border m-1 shadow-sm rounded-3 h-100'>
														<div class="d-flex flex-column">
															<label class="p-2 fs-6 fw-normal text-muted">{{ $label }}</label>
															@if(isset($parametros[$key]))
																@php
																	$param = $parametros[$key];
																	$value = $param[$empresa[$key]] ?? 'N/A';
																@endphp
																<div class="px-2 pb-2 fs-6 fw-light text-break">{{ capitalize($value) }}</div>
															@else
																<div class="px-2 pb-2 fs-6 fw-light text-break">{{ capitalize($empresa[$key] ?? 'N/A') }}</div>
															@endif
														</div>
													</div>
												</div>
											@endforeach
										@else
											<div class="alert alert-warning">No se encontraron datos de la empresa.</div>
										@endif
									</div>
								</div>
							</div>
						</div>

						<!-- Sección Trayectorias -->
						<div class="section-title d-flex align-items-center mt-4">
							<div class="d-flex align-items-center">
								<i class="fas fa-history me-2 text-primary"></i>
								<h5 class="mb-0 fw-bolder">Trayectoria de la Empresa</h5>
							</div>
						</div>
						<div class="card shadow-sm border-0 mb-3">
							<div class="card-body">
								<div class="table-responsive table-container mb-0">
									<table class="table table-striped table-hover align-middle">
										<thead>
											<tr>
												<th scope="col">NIT</th>
												<th scope="col">Fecha Afiliación</th>
												<th scope="col">Fecha Retiro</th>
												<th scope="col">Calificación Empresa</th>
											</tr>
										</thead>
										<tbody>
											@if(isset($trayectorias) && is_array($trayectorias) && count($trayectorias) > 0)
												@foreach($trayectorias as $trayectoria)
													<tr>
														<td>{{ $trayectoria['nit'] ?? 'N/A' }}</td>
														<td>{{ $trayectoria['fecafi'] ?? 'N/A' }}</td>
														<td>{{ $trayectoria['fecret'] ?? 'N/A' }}</td>
														<td>{{ $trayectoria['calemp'] ?? 'N/A' }}</td>
													</tr>
												@endforeach
											@else
												<tr>
													<td colspan="4" class="text-center">No se encontraron trayectorias.</td>
												</tr>
											@endif
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<!-- Sección Sucursales de la Empresa -->
						<div class="section-title mt-4 d-flex align-items-center">
							<div class="d-flex align-items-center">
								<i class="fas fa-store-alt me-2 text-primary"></i>
								<h5 class="mb-0 fw-bolder">Sucursales de la Empresa</h5>
							</div>
						</div>
						<div class="card shadow-sm border-0">
							<div class="card-body">
								<div class="data-grid">
									<div class="col-12"> <!-- Contenedor para cada sucursal -->
										<div class="card border-0 shadow-sm">
											@if(isset($sucursales) && is_array($sucursales) && count($sucursales) > 0)
												@foreach($sucursales as $sucursal)
													<div class="card-header bg-light d-flex align-items-center">
														<h6 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Sucursal: {{ $sucursal['codsuc'] ?? 'N/A' }}</h6>
													</div>
													<div class="card-body">
														<div class="row g-3">
															@php
																$sucursal_properties = [
																	'Sucursal' => 'detalle',
																	'Dirección' => 'direccion',
																	'Código Ciudad' => 'codciu',
																	'Teléfono' => 'telefono',
																	'Fax' => 'fax',
																	'Código Zona' => 'codzon',
																	'Oficina Afiliación' => 'ofiafi',
																	'Nombre Contacto' => 'nomcon',
																	'Email' => 'email',
																	'Calificación Sucursal' => 'calsuc',
																	'Código Actividad' => 'codact',
																	'Indice de Aportes' => 'codind',
																	'Trabajadores Aportantes' => 'traapo',
																	'Valor Aporte' => 'valapo',
																	'Actividad Aprobada' => 'actapr',
																	'Fecha Afiliación' => 'fecafi',
																	'Fecha Cambio' => 'feccam',
																	'Estado' => 'estado',
																	'Resultado Estado' => 'resest',
																	'Código Estado' => 'codest',
																	'Fecha Estado' => 'fecest',
																	'Total Trabajadores' => 'tottra',
																	'Total Aportes' => 'totapo',
																	'Tiempo Transcurrido' => 'tietra',
																	'Observación' => 'observacion',
																	'Pagadora' => 'pagadora',
																];
															@endphp
															@foreach($sucursal_properties as $label => $key)
																<div class="col-md-4">
																	<div class='box border m-1 shadow-sm rounded-3 h-100'>
																		<div class="d-flex flex-column">
																			<label class="p-2 fs-6 fw-normal text-muted">{{ $label }}</label>
																			@if(isset($parametros[$key]))
																				@php
																					$param = $parametros[$key];
																					$value = $param[$sucursal[$key]] ?? 'N/A';
																				@endphp
																				<div class="px-2 pb-2 fs-6 fw-light text-break">{{ capitalize($value) }}</div>
																			@else
																				<div class="px-2 pb-2 fs-6 fw-light text-break">{{ capitalize($sucursal[$key] ?? 'N/A') }}</div>
																			@endif
																		</div>
																	</div>
																</div>
															@endforeach
														</div>
													</div>
												@endforeach
											@else
												<div class="col-12">
													<div class="alert alert-warning">No se encontraron datos de sucursales.</div>
												</div>
											@endif
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
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
<style>
	.section-title {
		margin-bottom: 20px;
		padding: 0;
		border-left: none;
		background-color: transparent;
	}
	.estado-metric-card {
		display: flex;
		align-items: center;
		gap: 0.75rem;
		padding: 0.75rem 0.9rem;
		border-radius: 0.75rem;
		border: 1px solid #e9ecef;
		background-color: #ffffff;
		box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04);
	}
	.estado-metric-icon {
		width: 40px;
		height: 40px;
		border-radius: 0.75rem;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1rem;
	}
	.estado-metric-content {
		flex: 1;
		min-width: 0;
	}
	.estado-metric-label {
		font-size: 0.75rem;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: .04em;
		color: #6c757d;
		margin-bottom: 0.1rem;
	}
	.estado-metric-value {
		font-size: 0.95rem;
		font-weight: 600;
		color: #212529;
	}
	.estado-metric-value .badge {
		font-size: 0.75rem;
		padding: 0.4em 0.65em;
	}
    .data-grid {
        margin-bottom: 30px;
    }
    .data-grid .row {
        margin-bottom: 10px;
    }

    .data-grid .label {
        text-align: right;
        font-size: 1.09rem;
    }

    .table-container {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endpush