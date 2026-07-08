@if (count($pensionados) == 0 && (!($paginated ?? false) || ($total ?? 0) === 0))
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($pensionados as $solicitud)
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => capitalize($solicitud['razsoc']),
        'subtitulo' => 'CC ' . $solicitud['cedtra'] . ' · ' . ($solicitud['tipo_persona'] ?? '') . ' · ' . ($solicitud['detalle_zona'] ?? ''),
        'fecha' => $solicitud['fecest'] ?? '',
    ])
@endforeach
