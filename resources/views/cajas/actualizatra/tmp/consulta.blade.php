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