@php
    $fechaSolicitud = $solicitud['fecsol'] ?? '';

    $fechaAprobacion = ($solicitud['estado'] ?? '') === 'A'
        ? (($solicitud['fecapr'] ?? '') ?: ($solicitud['fecest'] ?? ''))
        : '';

    $searchText = strtolower(
        ($solicitud['ruuid'] ?? '') . ' ' .
        ($titulo ?? '') . ' ' .
        ($subtitulo ?? '') . ' ' .
        ($solicitud['estado_detalle'] ?? '') . ' ' .
        $fechaSolicitud . ' ' .
        $fechaAprobacion
    );
@endphp

<article
    class="solicitud-card"
    data-search="{{ $searchText }}"
    data-estado="{{ $solicitud['estado'] ?? '' }}"
>
    <div class="solicitud-card__header">
        <span class="solicitud-card__radicado">{{ $solicitud['ruuid'] ?? '—' }}</span>
        <span class="solicitud-card__estado solicitud-card__estado--{{ strtolower($solicitud['estado'] ?? 'x') }}">
            {{ $solicitud['estado_detalle'] ?? 'Sin estado' }}
        </span>
    </div>

    <h3 class="solicitud-card__titulo">{{ $titulo }}</h3>

    @if (!empty($subtitulo))
        <p class="solicitud-card__subtitulo">{{ $subtitulo }}</p>
    @endif

    <p class="solicitud-card__fecha" title="Fecha de solicitud">
        <i class="far fa-calendar-alt" aria-hidden="true"></i>
        Solicitud: {{ $fechaSolicitud ?: '—' }}
    </p>

    @if (!empty($fechaAprobacion))
        <p class="solicitud-card__fecha" title="Fecha de aprobación">
            <i class="far fa-calendar-check" aria-hidden="true"></i>
            Aprobación: {{ $fechaAprobacion }}
        </p>
    @endif

    @if (($solicitud['estado'] ?? '') !== 'A')
        <div class="solicitud-card__actions btn-group" role="group" aria-label="Acciones de solicitud">
            @switch($solicitud['estado'])
                @case('T')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                        <i class="fas fa-user-edit" aria-hidden="true"></i> Editar
                    </button>
                    @break
                @case('D')
                    <button type="button" class="btn btn-info btn-sm" data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                        <i class="fas fa-eye" aria-hidden="true"></i> Corregir
                    </button>
                    @break
                @case('X')
                    <button type="button" class="btn btn-secondary btn-sm" data-toggle="event-proceso" data-cid="{{ $solicitud['id'] }}">
                        <i class="fas fa-ban" aria-hidden="true"></i> Rechazado
                    </button>
                    @break
                @case('P')
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="event-detalle" data-cid="{{ $solicitud['id'] }}">
                        <i class="fas fa-eye" aria-hidden="true"></i> Seguimiento
                    </button>
                    @break
                @default
                    <button type="button" class="btn btn-light btn-sm" disabled>
                        <i class="fas fa-times" aria-hidden="true"></i> Sin acción
                    </button>
            @endswitch

            <button type="button" class="btn btn-danger btn-sm" data-toggle="cancel-solicitud" data-cid="{{ $solicitud['id'] }}">
                <i class="fas fa-trash" aria-hidden="true"></i> Borrar
            </button>
        </div>
    @endif
</article>
