@php
    $searchText = strtolower(
        ($solicitud['ruuid'] ?? '') . ' ' .
        ($titulo ?? '') . ' ' .
        ($subtitulo ?? '') . ' ' .
        ($solicitud['estado_detalle'] ?? '') . ' ' .
        ($fecha ?? '')
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

    @if (!empty($fecha))
        <p class="solicitud-card__fecha">
            <i class="far fa-calendar-alt" aria-hidden="true"></i>
            {{ $fecha }}
        </p>
    @endif

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
            @case('A')
                <button type="button" class="btn btn-success btn-sm" data-toggle="event-show" data-cid="{{ $solicitud['id'] }}">
                    <i class="fas fa-hand-pointer" aria-hidden="true"></i> Aprobada
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

        @if (($solicitud['estado'] ?? '') != 'A')
            <button type="button" class="btn btn-danger btn-sm" data-toggle="cancel-solicitud" data-cid="{{ $solicitud['id'] }}">
                <i class="fas fa-trash" aria-hidden="true"></i> Borrar
            </button>
        @endif
    </div>
</article>
