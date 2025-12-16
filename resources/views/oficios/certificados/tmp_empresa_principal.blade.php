{{-- 
    Plantilla de Certificado de Afiliación Principal para Trabajador
    Variables esperadas: $trabajador (object), $trayectorias (array), $fecha (string)
--}}
@include('oficios.certificados.styles')
<body>
<table class="header-table" width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td style="width: 22%;">
            &nbsp;
        </td>
        <td style="width: 78%; text-align: right;">
            <p class="title-company">CAJA DE COMPENSACIÓN FAMILIAR DEL CAQUETÁ</p>
            <p class="nit">NIT: 891.190.346-1</p>
        </td>
    </tr>
</table>
<table class="meta" width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td>Documento generado por Comfaca en Línea</td>
        <td class="right">Fecha de expedición: {{ $fecha }}</td>
    </tr>
</table>
<div class="document-title"><br/>EL JEFE DE DEPARTAMENTO DE APORTES Y SUBSIDIO FAMILIAR<br/> CERTIFICA</div>

@if($empleador)
<p class="body-text">Que revisada nuestra base de datos, se encontro que la empresa {{ $empleador->razsoc ?? 'N/A' }} con
Nit {{ $empleador->nit ?? 'N/A' }}, con representante legal {{ $empleador->repleg ?? 'N/A' }} se encuentra afiliada desde el {{ $empleador->fecha_afiliacion ?? 'N/A' }} y su estado actual es ACTIVO.</p>

<div class="col-md-6 col-xs-12">
    <p>
        <strong>EMPLEADOR</strong>
    </p>
    <div class="table-responsive">
        <table class="table table-sm table-borderless mb-0">
            <tbody>
                <tr>
                    <th style="width: 38%;"  scope="row" class="pe-2">Ciudad</th>
                    <td>{{ $empleador->ciudad ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="width: 38%;" scope="row" class="pe-2">Dirección</th>
                    <td>{{ $empleador->direccion ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="width: 38%;" scope="row" class="pe-2">Teléfono</th>
                    <td>{{ $empleador->telefono ?? $empleador->telpri ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="width: 38%;" scope="row" class="pe-2">Estado Actual en Aportes</th>
                    <td>{{ $empleador->estado_detalle ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="width: 38%;" scope="row" class="pe-2">Número Trabajadores Afiliados</th>
                    <td>{{ $empleador->numtrab ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="width: 38%;" scope="row" class="pe-2">Último Periodo Aportes</th>
                    <td>{{ $empleador->ultper ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<p class="body-text">La presente certificación se expide en la ciudad de <span class="bold">Florencia</span>, a solicitud del interesado,
    el día <span class="bold">{{ $fecha }}</span>.
</p>

<p class="note">Nota: Este documento es generado electrónicamente. Para validación y trazabilidad interna, el sistema conserva la evidencia
    de generación del certificado.
</p>
@else
<p class="body-text" style="text-align: center; color: #c00;">No se encontró información del trabajador.</p>
@endif
</body>