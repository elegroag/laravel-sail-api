@if (count($solicitudes) == 0 && (!($paginated ?? false) || ($total ?? 0) === 0))
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($solicitudes as $solicitud)
    @php
        $titulo = ! empty($solicitud['razsoc'])
            ? ucfirst($solicitud['razsoc'])
            : ($solicitud['tipact_detalle'] ?? 'Actualización de datos');
        $nit = $solicitud['nit'] ?? ($solicitud['documento'] ?? '—');
        $tipoEmpresa = $solicitud['tipo'] ?? '';
        $subtitulo = 'NIT ' . $nit . ' · ' . ($solicitud['tipact_detalle'] ?? 'Empresa');
        if ($tipoEmpresa !== '') {
            $subtitulo .= ' · Tipo ' . $tipoEmpresa;
        }
    @endphp
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => $titulo,
        'subtitulo' => $subtitulo,
        'fecha' => $solicitud['fecest'] ?? '',
    ])
@endforeach
