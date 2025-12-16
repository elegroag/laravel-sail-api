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
<div class="document-title">
    <br/>
    Certificado de multiafiliación</div>

@if($trabajador)
<p class="body-text">Para los fines pertinentes, se certifica que el(la) señor(a)
    <span class="bold">{{ $trabajador->nomtra ?? $trabajador->nombre ?? 'N/A' }}</span>, identificado(a) con cédula de ciudadanía
    No. <span class="bold">{{ $trabajador->cedtra ?? $trabajador->cedula ?? 'N/A' }}</span>, registra afiliación en esta Entidad.
</p>

<p class="body-text">Condición de afiliación: <span class="bold">{{ $trabajador->tipafi ?? 'DEPENDIENTE' }}</span>.
    Estado: <span class="bold">{{ $trabajador->estado_detalle ?? 'ACTIVO' }}</span>.
    Categoría: <span class="bold">{{ $trabajador->codcat ?? 'N/A' }}</span>.
    A continuación se relaciona la multiafiliación del trabajador:
</p>

<p style="font-weight: bold">Multiafiliaciones del trabajador</p>
<div class="table-container" style="margin-bottom: 1px;">
    <table class="data-table" width="100%" border="0" cellpadding="2" cellspacing="0">
        <thead>
            <tr>
                <th width="15%" style="width: 15%; text-align: center;">NIT</th>
                <th width="40%" style="width: 45%; text-align: center;">Razón Social</th>
                <th width="15%" style="width: 15%; text-align: center;" class="center">Estado</th>
                <th width="15%" style="width: 15%; text-align: center;" class="center">Fecha Afiliación</th>
                <th width="15%" style="width: 15%; text-align: center;" class="center">Fecha Retiro</th>
                
            </tr>
        </thead>
        <tbody>
            @forelse ($multiAfiliacion as $multi)
            <tr>
                <td width="15%" style="width: 15%; text-align: center">{{ $multi->nit ?? 'N/A' }}</td>
                <td width="45%" style="width: 45%;">{{ $multi->razsoc ?? 'N/A' }}</td>
                <td width="15%" style="width: 15%; text-align: center" class="center">{{ $multi->estado_detalle ?? 'N/A' }}</td>
                <td width="15%" style="width: 15%; text-align: center" class="center">{{ $multi->fecafi ?? 'N/A' }}</td>
                <td width="15%" style="width: 15%; text-align: center" class="center">{{ $multi->fecret ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="center">No hay multiafiliaciones registradas</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<table class="data-section" width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td class="label" width="35%" style="width: 35%;"><strong>Afiliación principal</strong></td>
    </tr>
    <tr>
        <td class="label" width="35%" style="width: 35%;">NIT:</td>
        <td class="value" width="65%" style="width: 65%;">{{ $trabajador->nit ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label" width="35%" style="width: 35%;">Razón social:</td>
        <td class="value" width="65%" style="width: 65%;">{{ $trabajador->razsoc ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label" width="35%" style="width: 35%;">Fecha afiliación:</td>
        <td class="value" width="65%" style="width: 65%;">{{ $trabajador->fecafi ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label" width="35%" style="width: 35%;">Fecha retiro:</td>
        <td class="value" width="65%" style="width: 65%;">{{ $trabajador->fecret ?? '-' }}</td>
    </tr>
</table>

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