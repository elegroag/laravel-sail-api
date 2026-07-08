@if (count($empresas) == 0)
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($empresas as $solicitud)
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => capitalize($solicitud['razsoc']),
        'subtitulo' => 'CC ' . $solicitud['cedtra'] . ' · ' . ($solicitud['tipo_persona'] ?? '') . ' · ' . ($solicitud['detalle_zona'] ?? ''),
        'fecha' => $solicitud['fecest'] ?? '',
    ])
@endforeach
