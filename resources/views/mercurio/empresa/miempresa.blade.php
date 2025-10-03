@extends('layouts.bone')

@section('content')
<div class="col-12 mt-3">
    <div class="row justify-content-between">
        <div class="col-md-6">
            <div class="card mb-3 border-primary">
                <div class="card-header">
                    <h5 class="mb-0 fw-bolder">Estado actual</h5>
                </div>
                <div class="card-body">
                    @if(isset($empresa) && is_array($empresa))
                        <div class="row">
                            <div class="col-12">
                                <strong>Estado:</strong> {{ isset($empresa['estado']) ? htmlspecialchars($parametros['estado'][$empresa['estado']]) : 'N/A' }}
                            </div>
                            <div class="col-12">
                                <strong>Razón social:</strong> {{ isset($empresa['razsoc']) ? htmlspecialchars($empresa['razsoc']) : 'N/A' }}
                            </div>
                            <div class="col-12">
                                <strong>Total sucursales:</strong> {{ isset($sucursales) && is_array($sucursales) ? count($sucursales) : 'N/A' }}
                            </div>
                            <div class="col-12">
                                @php
                                    $ultima_aprobacion = 'N/A';
                                    if (isset($trayectorias) && is_array($trayectorias) && count($trayectorias) > 0) {
                                        $ultima_trayectoria = end($trayectorias);
                                        $ultima_aprobacion = $ultima_trayectoria['fecafi'] ?? 'N/A';
                                    }
                                @endphp
                                <strong>Última aprobación trayectoria:</strong> {{ htmlspecialchars($ultima_aprobacion) }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No se encontraron datos de la empresa para mostrar el resumen.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3 border-primary">
                <div class="card-body">
                    <a href="{{ route('empresa.index') }}" class="img-link">
                        <img src="{{ asset('img/Mercurio/empresas.jpg') }}" class="img img-center" width="130px" alt="Afiliación de empresa">
                        <p class="text-muted text-center">Afiliación de empresa aquí</p>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            @if($empresa['estado'] === 'I')
                <a href="{{ route('empresa.index') }}" class="img-link">
                    <div class="alert alert-warning m-3" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x mr-2"></i> Realiza la solicitud de activación de la empresa para que pueda hacer afiliación de trabajadores y beneficiarios.
                    </div>
                </a>
            @endif
        </div>

        <div class="col-xs-12">
            <div class="card">
                <div class="card-body">
                    <div id="app">
                        <!-- Sección Datos de la Empresa -->
                        <div class="section-title">
                            <h5 class="mb-0 fw-bolder">Datos de la empresa</h5>
                        </div>
                        <div class="data-grid mb-3">
                            <div class="row justify-content-between">
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
                                            'Empresa pagadora de pesiones' => 'pagadora',
                                            'Código documento rep. legal' => 'coddocrepleg',
                                            'Primer apellido rep. legal' => 'priaperepleg',
                                            'Segundo apellido rep. legal' => 'segaperepleg',
                                            'Primer nombre rep. legal' => 'prinomrepleg',
                                            'Segundo nombre rep. legal' => 'segnomrepleg',
                                        ];
                                    @endphp
                                    @foreach($properties as $label => $key)
                                        <div class="col-sm-2 col-md-4 col-lg-3">
                                            <div class='box border m-1'>
                                                <div class="d-flex flex-row">
                                                    <label class="p-1 fs-6 fw-normal">{{ $label }}:</label>
                                                    @if(isset($parametros[$key]))
                                                        @php
                                                            $param = $parametros[$key];
                                                            $value = $param[$empresa[$key]] ?? 'N/A';
                                                        @endphp
                                                        <div class="p-1 fs-6 fw-light">{{ capitalize($value) }}</div>
                                                    @else
                                                        <div class="p-1 fs-6 fw-light">{{ capitalize($empresa[$key] ?? 'N/A') }}</div>
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

                        <!-- Sección Trayectorias -->
                        <div class="section-title">
                            <h5 class="mb-0 fw-bolder">Trayectorias</h5>
                        </div>
                        <div class="table-container mb-4">
                            <table class="table table-striped table-hover">
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

                        <!-- Sección Datos de la sucursal -->
                        <div class="section-title mt-2">
                            <h5 class="mb-0 fw-bolder">Datos de las sucursales</h5>
                        </div>
                        <div class="data-grid">
                            <div class="col-12"> <!-- Contenedor para cada sucursal -->
                                <div class="card">
                                @if(isset($sucursales) && is_array($sucursales) && count($sucursales) > 0)
                                    @foreach($sucursales as $sucursal)
                                        <div class="card-header">
                                            <h6 class="fw-bold">Sucursal: {{ $sucursal['codsuc'] ?? 'N/A' }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
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
                                                        <div class='box border m-1'>
                                                            <div class="d-flex flex-row">
                                                                <label class="p-1 fs-6 fw-normal">{{ $label }}:</label>
                                                                @if(isset($parametros[$key]))
                                                                    @php
                                                                        $param = $parametros[$key];
                                                                        $value = $param[$sucursal[$key]] ?? 'N/A';
                                                                    @endphp
                                                                    <div class="p-1 fs-6 fw-light">{{ capitalize($value) }}</div>
                                                                @else
                                                                    <div class="p-1 fs-6 fw-light">{{ capitalize($sucursal[$key] ?? 'N/A') }}</div>
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
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
<link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
<style>
    .section-title {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-left: 5px solid #0de1fd;
        margin-bottom: 20px;
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