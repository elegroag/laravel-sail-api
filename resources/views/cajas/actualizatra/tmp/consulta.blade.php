<div class="card mb-4 request-info-card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Información personal</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="prinom" class="form-label text-muted small mb-1">Primer nombre</label>
                    <div class="form-control bg-light">{{ $datostra['prinom'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="segnom" class="form-label text-muted small mb-1">Segundo nombre</label>
                    <div class="form-control bg-light">{{ $datostra['segnom'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="priape" class="form-label text-muted small mb-1">Primer apellido</label>
                    <div class="form-control bg-light">{{ $datostra['priape'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="segape" class="form-label text-muted small mb-1">Segundo apellido</label>
                    <div class="form-control bg-light">{{ $datostra['segape'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="expedicion" class="form-label text-muted small mb-1">Fecha expedición documento</label>
                    <div class="form-control bg-light">{{ $datostra['expedicion'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="telefono" class="form-label text-muted small mb-1">Teléfono</label>
                    <div class="form-control bg-light">{{ $datostra['telefono'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="celular" class="form-label text-muted small mb-1">Celular</label>
                    <div class="form-control bg-light">{{ $datostra['celular'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="email" class="form-label text-muted small mb-1">Email</label>
                    <div class="form-control bg-light">{{ $datostra['email'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="direccion" class="form-label text-muted small mb-1">Dirección de residencia</label>
                    <div class="form-control bg-light">{{ $datostra['direccion'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="dirlab" class="form-label text-muted small mb-1">Dirección de trabajo</label>
                    <div class="form-control bg-light">{{ $datostra['dirlab'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="codciu" class="form-label text-muted small mb-1">Ciudad residencia</label>
                    <div class="form-control bg-light">{{ isset($_codciu[$datostra['codciu']]) ? $_codciu[$datostra['codciu']] : '' }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="codzon" class="form-label text-muted small mb-1">Zona trabajo</label>
                    <div class="form-control bg-light">{{ isset($_codzon[$datostra['codzon']]) ? $_codzon[$datostra['codzon']] : '' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 request-info-card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Información del responsable</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="cedtra" class="form-label text-muted small mb-1">N° identificación</label>
                    <div class="form-control bg-light">{{ $datostra['cedtra'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_prinom" class="form-label text-muted small mb-1">Primer nombre</label>
                    <div class="form-control bg-light">{{ $datostra['respo_prinom'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_segnom" class="form-label text-muted small mb-1">Segundo nombre</label>
                    <div class="form-control bg-light">{{ $datostra['respo_segnom'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_priape" class="form-label text-muted small mb-1">Primer apellido</label>
                    <div class="form-control bg-light">{{ $datostra['respo_priape'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_segape" class="form-label text-muted small mb-1">Segundo apellido</label>
                    <div class="form-control bg-light">{{ $datostra['respo_segape'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_telefono" class="form-label text-muted small mb-1">Teléfono</label>
                    <div class="form-control bg-light">{{ $datostra['respo_telefono'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_celular" class="form-label text-muted small mb-1">Celular</label>
                    <div class="form-control bg-light">{{ $datostra['respo_celular'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="form-group">
                    <label for="respo_email" class="form-label text-muted small mb-1">Email</label>
                    <div class="form-control bg-light">{{ $datostra['respo_email'] }}</div>
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
