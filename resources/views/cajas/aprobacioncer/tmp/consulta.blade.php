<div class="card mb-4 request-info-card">
	<div class="card-header bg-light">
		<h5 class="mb-0">Información del certificado</h5>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Cedula de trabajador</label>
					<div class="form-control bg-light">{{$mercurio45->getCedtra()}}</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombres Completos</label>
					<div class="form-control bg-light">{{$mercurio45->getNombre()}}</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4">
				<div class="form-group">
					<label class="form-label text-muted small mb-1">Nombre del certificado</label>
					<div class="form-control bg-light">{{$mercurio45->getNomcer()}}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
    .request-info-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.2s ease;
        background: white;
    }

    .request-info-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .request-info-card .card-header {
        font-weight: 600;
        padding: 0.875rem 1.25rem;
        border-bottom: none;
        font-size: 0.95rem;
    }

    .request-info-card .form-group {
        margin-bottom: 0.75rem;
    }

    .request-info-card .form-label {
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.375rem;
        display: block;
    }

    .request-info-card .form-control {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        color: #374151;
        transition: all 0.15s ease;
        min-height: 20px;
        box-shadow: none;
        font-size: 0.875rem;
        line-height: 1.25rem;
        background-color:rgb(255, 255, 255);
    }

    .request-info-card .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .request-info-card .form-control:hover:not(:focus) {
        border-color: #d1d5db;
    }

    .request-info-card .card-body {
        padding: 1.25rem;
        background-color: #f9fafb;
    }

    .request-info-card .bg-light {
        background-color: #f9fafb !important;
    }

    .request-info-card .info-field {
        background-color: white;
        border-left: 3px solid #2563eb;
        border-radius: 6px;
        padding: 0.75rem;
        transition: all 0.15s ease;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .request-info-card .info-field:hover {
        border-left-color: #1d4ed8;
        background-color: #f8faff;
        transform: translateX(2px);
    }

    .request-info-card .info-field:last-child {
        margin-bottom: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .request-info-card .card-body {
            padding: 1rem;
        }

        .request-info-card .form-group {
            margin-bottom: 0.625rem;
        }

        .request-info-card .info-field {
            padding: 0.625rem;
            margin-bottom: 0.625rem;
        }
    }

    /* Animation optimization */
    .request-info-card,
    .request-info-card .form-control,
    .request-info-card .info-field {
        will-change: transform;
    }
</style>
