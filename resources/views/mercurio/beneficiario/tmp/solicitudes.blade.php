@php
    $parentescos = parentesco_array();
@endphp

@if (count($beneficiarios) == 0)
    <div class="solicitudes-grid__empty" role="status">
        ¡No hay solicitudes disponibles para mostrar!
    </div>
@endif

@foreach ($beneficiarios as $solicitud)
    @php
        $nombre = ucwords(strtolower($solicitud['prinom'] . ' ' . $solicitud['segnom'] . ' ' . $solicitud['priape'] . ' ' . $solicitud['segape']));
        $parentesco = $parentescos[$solicitud['parent']] ?? 'Parentesco';
    @endphp
    @include('mercurio/templates/tmp_solicitud_card', [
        'solicitud' => $solicitud,
        'titulo' => $nombre,
        'subtitulo' => $solicitud['numdoc'] . ' · ' . $parentesco,
        'fecha' => $solicitud['fecsol'] ?? '',
    ])
@endforeach
