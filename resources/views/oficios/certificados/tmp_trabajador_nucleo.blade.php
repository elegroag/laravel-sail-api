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
<div class="document-title">
    <br/>
    Certificado de núcleo familiar</div>

@if($trabajador)
<p class="body-text">Para los fines pertinentes, se certifica que el(la) señor(a)
    <span class="bold">{{ $trabajador->nomtra ?? $trabajador->nombre ?? 'N/A' }}</span>, identificado(a) con cédula de ciudadanía
    No. <span class="bold">{{ $trabajador->cedtra ?? $trabajador->cedula ?? 'N/A' }}</span>, registra afiliación en esta Entidad.
</p>

<p class="body-text">Condición de afiliación: <span class="bold">{{ $trabajador->tipafi ?? 'DEPENDIENTE' }}</span>.
    Estado: <span class="bold">{{ $trabajador->estado_detalle ?? 'ACTIVO' }}</span>.
    Categoría: <span class="bold">{{ $trabajador->codcat ?? 'N/A' }}</span>.
    A continuación se relaciona el nucleo familiar registrado:
</p>

<p style="font-weight: bold">Conyuge compañero permanente</p>
<div class="table-container" style="margin-bottom: 1px;">
    <table class="data-table" width="100%" border="0" cellpadding="2" cellspacing="0">
        <thead>
            <tr>
                <th width="16%" style="width: 16%; text-align: center;">Identificación</th>
                <th width="48%" style="width: 48%; text-align: center;">Nombre completo</th>
                <th width="18%" style="width: 18%; text-align: center;" class="center">Fecha Afiliación</th>
                <th width="18%" style="width: 18%; text-align: center;" class="center">Fecha Retiro</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($conyuges as $conyuge)
            <tr>
                <td width="16%" style="width: 16%; text-align: center">{{ $conyuge->cedcon ?? $conyuge->cedula ?? 'N/A' }}</td>
                <td width="48%" style="width: 48%;">{{ $conyuge->nomcony ?? 'N/A' }}</td>
                <td width="18%" style="width: 18%; text-align: center" class="center">{{ $conyuge->fecafi ?? 'N/A' }}</td>
                <td width="18%" style="width: 18%; text-align: center" class="center">{{ $conyuge->fecest ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="center">No hay conyuges registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p style="font-weight: bold">Beneficiarios afiliados</p>
<div class="table-container" style="margin-bottom: 1px;">
    <table class="data-table" width="100%" border="0" cellpadding="2" cellspacing="0">
        <thead>
            <tr>
                <th width="12%" style="width: 16%; text-align: center;">Parentesco</th>
                <th width="15%" style="width: 16%; text-align: center;">Identificación</th>
                <th width="48%" style="width: 48%; text-align: center;">Nombre completo</th>
                <th width="12%" style="width: 18%; text-align: center;" class="center">Fecha Afiliación</th>
                <th width="12%" style="width: 18%; text-align: center;" class="center">Fecha Retiro</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($beneficiarios as $beneficiario)
            <tr>
                <td width="12%" style="width: 16%; text-align: center">{{ $beneficiario->parent_detalle ?? 'N/A' }}</td>
                <td width="15%" style="width: 16%; text-align: center">{{ $beneficiario->documento ?? 'N/A' }}</td>
                <td width="48%" style="width: 48%;">{{ $beneficiario->nomben ?? 'N/A' }}</td>
                <td width="12%" style="width: 18%; text-align: center" class="center">{{ $beneficiario->fecafi ?? 'N/A' }}</td>
                <td width="12%" style="width: 18%; text-align: center" class="center">{{ $beneficiario->fecest ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="center">No hay beneficiarios registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>
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