@extends('layouts.auth')

@section('title', 'Verificación de carnet | Comfaca')
@section('application', 'web')

@push('styles')
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        .verifica-card {
            max-width: 440px;
            width: 100%;
            border: 0;
            border-radius: 20px;
            overflow: hidden;
        }

        .verifica-header {
            padding: 22px 24px 18px;
            text-align: center;
            background: linear-gradient(135deg, #22b7bd 0%, #2e8b4e 55%, #8cc63f 100%);
        }

        .verifica-header img {
            height: 38px;
            background: #fff;
            border-radius: 12px;
            padding: 6px 10px;
        }

        .verifica-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 14px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 2rem;
        }

        .verifica-ok { background: rgba(46, 139, 78, .14); color: #2e8b4e; }
        .verifica-warn { background: rgba(251, 99, 64, .14); color: #d9480f; }
        .verifica-error { background: rgba(245, 54, 92, .12); color: #c81e45; }

        .verifica-nombre {
            font-size: 1.1rem;
            font-weight: 700;
            color: #344767;
            letter-spacing: .02em;
        }

        .verifica-meta {
            font-size: .8rem;
            color: #7b8aa0;
        }
    </style>
@endpush

@section('content')
<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow verifica-card">
        <div class="verifica-header">
            <img src="{{ asset('img/comfaca-logo.png') }}" alt="Comfaca" />
        </div>
        <div class="card-body text-center p-4">
            @if (! $disponible)
                <div class="verifica-icon verifica-warn"><i class="fas fa-exclamation-triangle"></i></div>
                <h1 class="h5 mb-2">Verificación no disponible</h1>
                <p class="verifica-meta mb-0">No fue posible validar el carnet en este momento. Intenta de nuevo más tarde.</p>
            @elseif (! $resultado)
                <div class="verifica-icon verifica-error"><i class="fas fa-times"></i></div>
                <h1 class="h5 mb-2">Carnet no válido</h1>
                <p class="verifica-meta mb-0">El código escaneado no corresponde a un carnet vigente de Comfaca.</p>
            @elseif ($resultado['activo'])
                <div class="verifica-icon verifica-ok"><i class="fas fa-check"></i></div>
                <h1 class="h5 mb-3">Afiliado activo</h1>
                <p class="verifica-nombre mb-1">{{ $resultado['nombre'] }}</p>
                <p class="verifica-meta mb-0">Verificado el {{ $resultado['fecha'] }}</p>
            @else
                <div class="verifica-icon verifica-error"><i class="fas fa-user-slash"></i></div>
                <h1 class="h5 mb-3">Afiliación no vigente</h1>
                <p class="verifica-nombre mb-1">{{ $resultado['nombre'] }}</p>
                <p class="verifica-meta mb-1">Estado: {{ $resultado['estado_detalle'] }}</p>
                <p class="verifica-meta mb-0">Verificado el {{ $resultado['fecha'] }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
