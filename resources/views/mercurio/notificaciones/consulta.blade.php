@extends('layouts.bone')

@push('styles')
<style>
    .noty-page-card {
        border-radius: 16px;
        overflow: hidden;
    }

    .noty-page-header {
        padding: 1.25rem 1.5rem 0.5rem;
        border-bottom: 1px solid #eef2f7;
    }

    .noty-page-title {
        margin: 0 0 0.35rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: #334155;
    }

    .noty-page-subtitle {
        margin: 0;
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.55;
    }

    .noty-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .noty-item {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.15rem;
        background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .noty-item:hover {
        border-color: rgba(13, 110, 253, 0.25);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .noty-item.is-pending {
        border-left: 4px solid #0d6efd;
    }

    .noty-item-top {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.55rem;
    }

    .noty-item-title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #1e293b;
    }

    .noty-item-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem 1rem;
        font-size: 0.8rem;
        color: #64748b;
    }

    .noty-item-meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .noty-item-descri {
        margin: 0.35rem 0 0;
        font-size: 0.88rem;
        color: #475569;
        line-height: 1.55;
        word-break: break-word;
    }

    .noty-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #64748b;
    }

    .noty-empty i {
        font-size: 2rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center m-0 mt-3">
    <div class="col-12 col-xl-9 col-lg-10">
        <div class="card mb-0 shadow-sm border-0 noty-page-card">
            <div class="card-header border-0 noty-page-header">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                    <div>
                        <h2 class="noty-page-title">Mis notificaciones</h2>
                        <p class="noty-page-subtitle">
                            Consulta los avisos asociados a tu cuenta en Comfaca En Línea.
                        </p>
                    </div>
                    @if(($pendientes ?? 0) > 0)
                        <span class="badge bg-primary rounded-pill align-self-center">
                            {{ $pendientes }} pendiente{{ $pendientes === 1 ? '' : 's' }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body px-3 px-md-4 py-4">
                @if($notificaciones->isEmpty())
                    <div class="noty-empty">
                        <i class="ni ni-bell-55 d-block"></i>
                        <p class="mb-0">No tienes notificaciones registradas por el momento.</p>
                    </div>
                @else
                    <div class="noty-list">
                        @foreach($notificaciones as $notificacion)
                            <article class="noty-item {{ ($notificacion->estado ?? '') === 'P' ? 'is-pending' : '' }}">
                                <div class="noty-item-top">
                                    <h3 class="noty-item-title">{{ $notificacion->titulo ?: 'Sin título' }}</h3>
                                    @php
                                        $estado = $notificacion->estado ?? '';
                                        $badgeClass = match ($estado) {
                                            'P' => 'bg-info',
                                            'L' => 'bg-secondary',
                                            'A' => 'bg-success',
                                            'R' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $notificacion->estado_detalle }}</span>
                                </div>
                                <div class="noty-item-meta">
                                    <span title="Asesor">
                                        <i class="fas fa-user-tie"></i>
                                        {{ $notificacion->asesor }}
                                    </span>
                                    <span title="Fecha">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $notificacion->dia ?: 'N/A' }}
                                    </span>
                                    <span title="Hora">
                                        <i class="fas fa-clock"></i>
                                        {{ $notificacion->hora ?: 'N/A' }}
                                    </span>
                                </div>
                                <div class="noty-item-descri">
                                    {!! $notificacion->descripcion ?: '<em>Sin descripción</em>' !!}
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
