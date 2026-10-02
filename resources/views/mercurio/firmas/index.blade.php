@extends('layouts.bone')

@section('content')
<div id='boneLayout'>
    <div class="col-12 col-xl-10 mx-auto mt-3">
        <div class="card mb-0 shadow-sm border-0 firma-page-card">
            <div class="card-header border-0 firma-page-header">
                <div>
                    <h2 class="firma-page-title">{{ $title ?? 'Firma digital' }}</h2>
                    <p class="firma-page-subtitle">
                        Verifica documentos digitales con la clave pública de COMFACA o recupera tu firma cuando lo necesites.
                    </p>
                </div>
            </div>

            <div class="card-body px-3 px-md-4 py-4">
                <div id="app" class="row firma-grid">
                    <div class="col-lg-5">
                        <section class="firma-section">
                            <div class="firma-section-title">
                                <i class="fas fa-key" aria-hidden="true"></i>
                                <span>Clave pública firma digital</span>
                            </div>
                            <p class="firma-section-text">
                                Para comprobar la autenticidad de un documento digital, utiliza el siguiente certificado público de firma digital.
                            </p>

                            <div class="firma-key-block">
                                <div class="firma-key-toolbar">
                                    <span class="firma-key-label">Certificado público</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="copyPublicKey">
                                        <i class="fas fa-copy mr-1" aria-hidden="true"></i>
                                        Copiar
                                    </button>
                                </div>
                                <pre class="firma-key-content" id="publicKeyContent">{{ $publicKey }}</pre>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-7">
                        <section class="firma-section">
                            <div class="firma-section-title">
                                <i class="fas fa-file-signature" aria-hidden="true"></i>
                                <span>Validar documento</span>
                            </div>
                            <p class="firma-section-text">
                                Arrastra un archivo firmado digitalmente o selecciónalo desde tu equipo para verificar su autenticidad.
                            </p>

                            <div id="fileUpload" class="file-container"></div>
                        </section>
                    </div>
                </div>

                <div class="firma-recovery-section">
                    <div class="d-flex justify-content-center">
                        <button type="button" id="toggleRecoveryForm" class="btn btn-outline-secondary">
                            <i class="fas fa-eye mr-1" aria-hidden="true"></i>
                            Mostrar recuperación de firma
                        </button>
                    </div>

                    <div class="card border-0 shadow-sm firma-recovery-card" id="recoveryFormCard" style="display: none;">
                        <div class="card-body p-4">
                            <div class="firma-section-title mb-3">
                                <i class="fas fa-unlock-alt" aria-hidden="true"></i>
                                <span>Recuperar firma digital</span>
                            </div>
                            <p class="firma-section-text mb-3">
                                Ingresa tu clave del sistema para recuperar tu firma digital. Las credenciales se enviarán a tu correo registrado.
                            </p>
                            <div class="form-row align-items-end">
                                <div class="col-sm-7 mb-2 mb-sm-0">
                                    <label for="systemKey" class="form-control-label">Clave del sistema</label>
                                    <input type="password"
                                           class="form-control"
                                           id="systemKey"
                                           name="systemKey"
                                           placeholder="Ingresa tu clave del sistema">
                                </div>
                                <div class="col-sm-5">
                                    <button type="button" id="recoverSignatureBtn" class="btn btn-primary btn-block">
                                        <i class="fas fa-key mr-1" aria-hidden="true"></i>
                                        Recuperar firma
                                    </button>
                                </div>
                            </div>
                            <div id="signatureResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.getElementById('copyPublicKey')?.addEventListener('click', async function () {
            const content = document.getElementById('publicKeyContent')?.textContent?.trim();
            if (!content) {
                return;
            }

            try {
                await navigator.clipboard.writeText(content);
                this.innerHTML = '<i class="fas fa-check mr-1" aria-hidden="true"></i>Copiado';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy mr-1" aria-hidden="true"></i>Copiar';
                }, 2000);
            } catch (error) {
                window.prompt('Copia la clave pública:', content);
            }
        });
    </script>
    <script src="{{ versioned_asset('mercurio/build/Firma.js') }}"></script>
@endpush

@push('styles')
<style>
    .firma-page-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .firma-page-header {
        padding: 1.25rem 1.5rem 0.5rem;
        border-bottom: 1px solid #eef2f7;
    }

    .firma-page-title {
        margin: 0 0 0.35rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #334155;
    }

    .firma-page-subtitle {
        margin: 0;
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.55;
    }

    /* El tema es Bootstrap 4: sin utilidades g-*, el espacio entre columnas apiladas se define aquí */
    .firma-grid {
        row-gap: 1.5rem;
    }

    .firma-section {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.15rem 1.25rem;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        background: #fff;
    }

    .firma-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: #334155;
    }

    .firma-section-title i {
        color: #0d6efd;
    }

    .firma-section-text {
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.55;
    }

    .firma-key-block {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        border: 1px solid #dbeafe;
        border-radius: 12px;
        overflow: hidden;
        background: #0f172a;
    }

    .firma-key-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.65rem 0.85rem;
        background: #1e293b;
        border-bottom: 1px solid #334155;
    }

    .firma-key-label {
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #cbd5e1;
    }

    .firma-key-content {
        flex: 1 1 auto;
        margin: 0;
        padding: 1rem;
        min-height: 180px;
        max-height: 320px;
        overflow: auto;
        font-size: 0.75rem;
        line-height: 1.6;
        color: #e2e8f0;
        background: transparent;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .firma-recovery-section {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px solid #eef2f7;
    }

    .firma-recovery-card {
        max-width: 720px;
        margin: 1rem auto 0;
    }

    .file-container {
        width: 100%;
    }

    .file-container .file-upload {
        width: 100%;
        display: block;
        margin: 0;
        transition: all 0.2s ease;
    }

    .file-container .file-upload > div {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        min-height: 200px;
        padding: 1.5rem;
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-container .file-upload:hover > div,
    .file-container.dragover .file-upload > div {
        border-color: #0d6efd;
        background: #f8fbff;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.12);
    }

    .file-container .file-upload > div > .firma-upload-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        background: #e7f1ff;
        color: #0d6efd;
        font-size: 1.25rem;
    }

    .file-container .file-upload > div > b {
        font-size: 1rem;
        font-weight: 700;
        color: #334155;
    }

    .file-container .file-upload > div > p,
    .file-container .file-upload > div > span {
        margin: 0;
        font-size: 0.875rem;
        color: #64748b;
    }

    .file-container .file-upload > div > div {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.9rem;
        margin-top: 0.35rem;
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        background: #fff;
    }

    .firma-table-wrap {
        margin-top: 1rem;
        overflow-x: auto;
        border: 1px solid #e9ecef;
        border-radius: 12px;
    }

    .firma-table-wrap > table {
        width: 100%;
        min-width: 480px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.84rem;
    }

    .firma-table-wrap > table thead th {
        white-space: nowrap;
        background: #f1f5f9;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid #e9ecef;
    }

    .firma-table-wrap > table tbody td {
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        color: #334155;
    }

    .firma-table-wrap > table tbody tr:last-child td {
        border-bottom: 0;
    }

    .firma-table-wrap > table tbody tr:hover {
        background: #f8fbff;
    }

    .firma-table-wrap > table tbody tr > td:nth-child(2),
    .firma-table-wrap > table tbody tr > td:nth-child(4) {
        max-width: 160px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .firma-table-wrap > table tbody tr > td:nth-child(3),
    .firma-table-wrap > table tbody tr > td:last-child {
        white-space: nowrap;
    }

    .firma-table-wrap > table tbody tr > td.no-file {
        text-align: center;
        color: #64748b;
    }

    .firma-table-wrap > table button {
        border-radius: 8px;
    }

    #signatureResult pre {
        max-height: 220px;
        overflow: auto;
        margin: 0.75rem 0 0;
        padding: 0.85rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        white-space: pre-wrap;
        word-break: break-all;
    }

    #signatureResult .firma-clave-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    #signatureResult .firma-clave-valor {
        display: inline-block;
        min-width: 8rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.9);
        color: #0f172a;
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.25em;
        text-align: center;
    }

    #signatureResult .firma-clave-reveal {
        user-select: none;
        -webkit-user-select: none;
        touch-action: none;
    }

    @media (max-width: 991.98px) {
        .firma-key-content {
            max-height: 220px;
        }
    }
</style>
@endpush
