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
    Certificado de Aportes</div>

@if($trabajador)
<p class="body-text">Para los fines pertinentes, se certifica que el(la) señor(a)
    <span class="bold">{{ $trabajador->nomtra ?? $trabajador->nombre ?? 'N/A' }}</span>, identificado(a) con cédula de ciudadanía
    No. <span class="bold">{{ $trabajador->cedtra ?? $trabajador->cedula ?? 'N/A' }}</span>, registra afiliación en esta Entidad.
</p>

<p class="body-text">A continuación se reporta los aportes realizados por planilla PILA:</p>

<div class="table-container">
    <table class="data-table" width="100%" border="0" cellpadding="2" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 7%; text-align: center;">Tipo</th>
                <th style="width: 8%; text-align: center;">Periodo</th>
                <th style="width: 10%; text-align: center;">Horas</th>
                <th style="width: 12%; text-align: center;">Fecha pago</th>
                <th style="width: 15%; text-align: center;">Valor nomina</th>
                <th style="width: 12%; text-align: center;">Valor aporte</th>
                <th style="width: 15%; text-align: center;">N. Ingreso</th>
                <th style="width: 15%; text-align: center;">N. Retiro</th>                
            </tr>
        </thead>
        <tbody>
            @forelse ($aportesPlanilla as $aporte)
            <tr>
                <td style="width:7%"> {{ $aporte->tippla ?? 'N/A' }}</td>
                <td style="width:8%"> {{ $aporte->perapo ?? 'N/A' }}</td>
                <td style="width:10%"> {{ $aporte->horas ?? 'N/A' }}</td>
                <td style="width:12%"> {{ $aporte->fecrec ?? 'N/A' }}</td>
                <td style="width:15%"> {{ $aporte->valnom ?? 'N/A' }}</td>
                <td style="width:12%"> {{ $aporte->valapo ?? 'N/A' }}</td>
                <td style="width:15%"> {{ $aporte->ingtra ?? '' }}</td>
                <td style="width:15%"> {{ $aporte->novret ?? '' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">No hay aportes registrados</td>
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