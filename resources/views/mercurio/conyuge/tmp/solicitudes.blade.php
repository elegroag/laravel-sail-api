@if (count($conyuges) == 0 && (!($paginated ?? false) || ($total ?? 0) === 0))
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($conyuges as $solicitud)
    @php
        $nombre = capitalize($solicitud['prinom'] . ' ' . $solicitud['segnom'] . ' ' . $solicitud['priape'] . ' ' . $solicitud['segape']);
    @endphp
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => $nombre,
        'subtitulo' => 'CC ' . $solicitud['cedcon'],
        'fecha' => $solicitud['fecsol'] ?? '',
    ])
@endforeach
