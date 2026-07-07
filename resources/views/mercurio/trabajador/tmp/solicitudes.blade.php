@if (count($trabajadores) == 0)
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($trabajadores as $solicitud)
    @php
        $nombre = capitalize($solicitud['prinom'] . ' ' . $solicitud['segnom'] . ' ' . $solicitud['priape'] . ' ' . $solicitud['segape']);
        $empresa = !empty($solicitud['razsoc']) ? ' · ' . $solicitud['razsoc'] : '';
    @endphp
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => $nombre,
        'subtitulo' => 'CC ' . $solicitud['cedtra'] . $empresa,
        'fecha' => $solicitud['fecsol'] ?? '',
    ])
@endforeach
