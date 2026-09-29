@extends('layouts.bone')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('mercurio/css/carnet.css') }}" />
@endpush

@push('scripts')
<script>
    (function () {
        const carnet = document.getElementById('carnet');
        const btnFlip = document.getElementById('btnFlip');
        if (!carnet || !btnFlip) {
            return;
        }
        btnFlip.addEventListener('click', function () {
            const flipped = carnet.classList.toggle('is-flipped');
            btnFlip.querySelector('span').textContent = flipped ? 'Ver frente' : 'Ver reverso';
        });
    })();
</script>
@endpush

@section('title', 'Carnet digital')

@section('content')
@php
    $trabajador = $carnet['trabajador'];
    $beneficiarios = $carnet['beneficiarios'];
@endphp
<div class="col-12 mt-3 px-3 carnet-page">
    <p class="carnet-page-subtitle pt-3">Presenta este carnet junto con tu documento de identidad para acceder a los servicios de Comfaca.</p>

    <div class="carnet-scene">
        <div class="carnet" id="carnet">
            <div class="carnet-inner">
                <section class="carnet-face carnet-front" aria-label="Frente del carnet">
                    <header class="carnet-header">
                        <svg class="carnet-wave carnet-wave-desktop" viewBox="0 0 640 96" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="carnetGradFront" x1="0" y1="0" x2="1" y2="0">
                                    <stop offset="0" stop-color="#1f5f36" />
                                    <stop offset=".6" stop-color="#2e8b4e" />
                                    <stop offset="1" stop-color="#8cc63f" />
                                </linearGradient>
                            </defs>
                            <path d="M0 0H640V58C560 88 470 44 380 62C290 80 220 96 130 78C80 68 40 70 0 80Z" fill="url(#carnetGradFront)" />
                            <path d="M380 62C470 44 560 88 640 58V70C560 98 470 56 380 74Z" fill="#5cc59b" opacity=".55" />
                        </svg>
                        <svg class="carnet-wave carnet-wave-mobile" viewBox="0 0 420 190" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="carnetGradMobile" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#22b7bd" />
                                    <stop offset=".55" stop-color="#2e8b4e" />
                                    <stop offset="1" stop-color="#8cc63f" />
                                </linearGradient>
                            </defs>
                            <path d="M0 0H420V150C340 190 270 130 190 150C120 168 60 180 0 160Z" fill="url(#carnetGradMobile)" />
                        </svg>
                        <div class="carnet-logo"><img src="{{ asset('img/comfaca-logo.png') }}" alt="Comfaca" /></div>
                        <div class="carnet-title">
                            <strong>CARNET DE AFILIADO</strong>
                            <span>Trabajador</span>
                        </div>
                    </header>

                    <div class="carnet-body">
                        <div class="carnet-identity">
                            <div class="carnet-avatar" aria-hidden="true">{{ $trabajador['iniciales'] }}</div>
                            <div>
                                <p class="carnet-name">{{ $trabajador['nombre'] }}</p>
                                <p class="carnet-doc">{{ $trabajador['tipo_documento'] }} <b>{{ $trabajador['documento'] }}</b></p>
                            </div>
                        </div>

                        <dl class="carnet-data">
                            <div class="carnet-span-2">
                                <dt><i class="fas fa-building"></i> Empresa</dt>
                                <dd title="{{ $trabajador['empresa'] }}">
                                    {{ $trabajador['empresa'] ?: '-' }}@if ($trabajador['nit']) · NIT {{ $trabajador['nit'] }}@endif
                                </dd>
                            </div>
                            <div>
                                <dt><i class="fas fa-layer-group"></i> Categoría</dt>
                                <dd>
                                    @if ($trabajador['categoria'])
                                        <span class="carnet-badge carnet-badge-categoria">Categoría {{ $trabajador['categoria'] }}</span>
                                    @else
                                        -
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt><i class="fas fa-user-check"></i> Estado</dt>
                                <dd>
                                    <span class="carnet-badge {{ $trabajador['estado'] === 'A' ? 'carnet-badge-activo' : 'carnet-badge-inactivo' }}">
                                        {{ $trabajador['estado_detalle'] ?: '-' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt><i class="fas fa-calendar-alt"></i> Afiliado desde</dt>
                                <dd>{{ $trabajador['fecha_afiliacion'] ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt><i class="fas fa-users"></i> Beneficiarios</dt>
                                <dd>{{ count($beneficiarios) }} {{ count($beneficiarios) === 1 ? 'registrado' : 'registrados' }}</dd>
                            </div>
                        </dl>

                        <div class="carnet-qr">
                            <div class="carnet-qr-code"><img src="{{ $qr }}" alt="Código QR de verificación del carnet" /></div>
                            <small>Escanea para verificar</small>
                        </div>
                    </div>

                    <footer class="carnet-footer">
                        <span><i class="fas fa-sync-alt"></i> Consultado el {{ $carnet['consultado'] }}</span>
                        <span><i class="fas fa-shield-alt"></i> Válido con documento de identidad</span>
                    </footer>
                </section>

                <section class="carnet-face carnet-back" aria-label="Reverso del carnet">
                    <header class="carnet-header">
                        <svg class="carnet-wave" viewBox="0 0 640 82" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="carnetGradBack" x1="0" y1="0" x2="1" y2="0">
                                    <stop offset="0" stop-color="#8cc63f" />
                                    <stop offset=".45" stop-color="#2e8b4e" />
                                    <stop offset="1" stop-color="#1f5f36" />
                                </linearGradient>
                            </defs>
                            <path d="M0 0H640V60C560 80 470 46 380 60C290 74 200 82 110 70C70 64 30 66 0 72Z" fill="url(#carnetGradBack)" />
                        </svg>
                        <div class="carnet-title">
                            <strong>NÚCLEO FAMILIAR</strong>
                            <span>Beneficiarios a cargo del afiliado</span>
                        </div>
                        <div class="carnet-logo"><img src="{{ asset('img/comfaca-logo.png') }}" alt="Comfaca" /></div>
                    </header>

                    <div class="carnet-back-body">
                        <h3 class="carnet-section-title">Beneficiarios ({{ count($beneficiarios) }})</h3>
                        @if (count($beneficiarios) > 0)
                            <ul class="carnet-beneficiarios">
                                @foreach ($beneficiarios as $beneficiario)
                                    <li>
                                        <span class="carnet-beneficiario-icon"><i class="fas {{ $beneficiario['icono'] }}"></i></span>
                                        <div>
                                            <div class="carnet-beneficiario-nombre">{{ $beneficiario['nombre'] }}</div>
                                            <div class="carnet-beneficiario-doc">{{ $beneficiario['tipo_documento'] }} {{ $beneficiario['documento'] }}</div>
                                        </div>
                                        <span class="carnet-beneficiario-parentesco">{{ $beneficiario['parentesco'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="carnet-empty">No registras beneficiarios activos.</p>
                        @endif
                    </div>

                    <p class="carnet-legal">
                        Este carnet es personal e intransferible. La información corresponde a la registrada en Comfaca en la fecha de consulta.
                        Su validez puede confirmarse escaneando el código QR del frente.
                    </p>
                </section>
            </div>
        </div>

        <div class="carnet-actions">
            <button type="button" class="carnet-btn-flip" id="btnFlip"><i class="fas fa-sync-alt"></i> <span>Ver reverso</span></button>
        </div>
    </div>
</div>
@endsection
