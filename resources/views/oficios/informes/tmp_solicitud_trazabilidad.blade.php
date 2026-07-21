{{--
    Informe PDF de solicitud con trazabilidad
    Variables: $fecha, $cabecera, $campos, $eventos
--}}
@include('oficios.certificados.styles')
<style>
    .section-title {
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 14px 0 6px 0;
        color: #1e3a5f;
        border-bottom: 1px solid #c5d4e8;
        padding-bottom: 3px;
    }
    .meta-table td {
        font-size: 9px;
        padding: 2px 4px;
    }
    .trace-table th {
        background-color: #e8eef7;
        font-size: 9px;
        text-align: left;
        padding: 4px;
    }
    .trace-table td {
        font-size: 9px;
        padding: 4px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }
</style>
<body>
<table class="header-table" width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td style="width: 22%;">&nbsp;</td>
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
    Informe de solicitud — trazabilidad
</div>

<div class="section-title">1. Datos de radicación</div>
<table class="data-section meta-table" width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr>
        <td class="label" width="35%">RUUID:</td>
        <td class="value" width="65%"><span class="bold">{{ $cabecera['ruuid'] }}</span></td>
    </tr>
    <tr>
        <td class="label">Tipo de afiliación:</td>
        <td class="value">{{ $cabecera['tipo_label'] }} ({{ $cabecera['tipopc'] }})</td>
    </tr>
    <tr>
        <td class="label"># Solicitud:</td>
        <td class="value">{{ $cabecera['id'] }}</td>
    </tr>
    <tr>
        <td class="label">Estado:</td>
        <td class="value">{{ $cabecera['estado'] }}</td>
    </tr>
    <tr>
        <td class="label">Fecha solicitud:</td>
        <td class="value">{{ $cabecera['fecsol'] ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Fecha aprobación:</td>
        <td class="value">{{ $cabecera['fecapr'] ?: ($cabecera['fecha_cierre'] ?: '—') }}</td>
    </tr>
    <tr>
        <td class="label">Identificación:</td>
        <td class="value">{{ trim(($cabecera['documento'] ?? '')) ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Nombres / razón:</td>
        <td class="value">{{ $cabecera['nombre'] ?: '—' }}</td>
    </tr>
    @if (!empty($cabecera['nit']) || !empty($cabecera['razsoc']))
    <tr>
        <td class="label">NIT aportante:</td>
        <td class="value">{{ $cabecera['nit'] ?: '—' }}</td>
    </tr>
    <tr>
        <td class="label">Razón social:</td>
        <td class="value">{{ $cabecera['razsoc'] ?: '—' }}</td>
    </tr>
    @endif
</table>

<div class="section-title">2. Datos de la solicitud</div>
@if (empty($campos))
<p class="body-text">No hay campos adicionales para este tipo de solicitud.</p>
@else
<table class="data-section" width="100%" border="0" cellpadding="2" cellspacing="0">
    @foreach ($campos as $label => $valor)
    <tr>
        <td class="label" width="35%">{{ $label }}:</td>
        <td class="value" width="65%">{{ is_scalar($valor) ? $valor : json_encode($valor, JSON_UNESCAPED_UNICODE) }}</td>
    </tr>
    @endforeach
</table>
@endif

<div class="section-title">3. Trazabilidad de eventos</div>
<table class="data-table trace-table" width="100%" border="0" cellpadding="3" cellspacing="0">
    <thead>
        <tr>
            <th width="18%">Fecha</th>
            <th width="22%">Estado</th>
            <th width="60%">Observación</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($eventos as $evento)
        <tr>
            <td width="18%">{{ $evento['fecha'] }}</td>
            <td width="22%">{{ $evento['estado'] }}</td>
            <td width="60%">{{ $evento['nota'] !== '' ? $evento['nota'] : '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="center">Sin eventos de seguimiento</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p class="note" style="margin-top: 16px; font-size: 8px; color: #666;">
    Este documento es generado electrónicamente para control interno. La trazabilidad se obtiene del registro de seguimiento de la solicitud.
</p>
</body>
