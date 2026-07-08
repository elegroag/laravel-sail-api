@if (count($solicitudes) == 0 && (!($paginated ?? false) || ($total ?? 0) === 0))
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($solicitudes as $solicitud)
    @php
        $nombre = trim(
            ($solicitud['prinom'] ?? '') . ' ' .
            ($solicitud['segnom'] ?? '') . ' ' .
            ($solicitud['priape'] ?? '') . ' ' .
            ($solicitud['segape'] ?? '')
        );
        $titulo = $nombre !== ''
            ? capitalize($nombre)
            : ($solicitud['tipact_detalle'] ?? 'Actualización de datos');
        $cedtra = $solicitud['cedtra'] ?? '—';
        $subtitulo = 'CC ' . $cedtra . ' · ' . ($solicitud['tipact_detalle'] ?? 'Trabajador');
    @endphp
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => $titulo,
        'subtitulo' => $subtitulo,
        'fecha' => $solicitud['fecsol'] ?? ($solicitud['fecest'] ?? ''),
    ])
@endforeach
