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
            height: 52px;
            width: auto;
            background: #fff;
            border-radius: 12px;
            padding: 6px 12px;
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

        .verifica-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 999px;
            background: rgba(34, 183, 189, .14);
            color: #138a8f;
            font-size: .8rem;
            font-weight: 700;
        }

        .verifica-nucleo {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
            text-align: left;
        }

        .verifica-nucleo-title {
            margin-bottom: 10px;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #2e8b4e;
        }

        .verifica-beneficiarios {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .verifica-beneficiarios li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
        }

        .verifica-beneficiarios li + li {
            border-top: 1px dashed #e9ecef;
        }

        .verifica-beneficiario-icon {
            flex: 0 0 32px;
            height: 32px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(140, 198, 63, .18);
            color: #2e8b4e;
            font-size: .85rem;
        }

        .verifica-beneficiario-info {
            flex: 1;
            min-width: 0;
        }

        .verifica-beneficiario-nombre {
            font-size: .85rem;
            font-weight: 700;
            color: #344767;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .verifica-beneficiario-parentesco {
            flex: 0 0 auto;
            padding: 2px 8px;
            border-radius: 999px;
            background: rgba(34, 183, 189, .12);
            color: #138a8f;
            font-size: .72rem;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
<div class="container py-5 d-flex justify-content-center">
    <div class="card shadow verifica-card">
        <div class="verifica-header">
            <img src="{{ asset('img/comfaca-logo.png') }}" srcset="{{ asset('img/comfaca-logo.png') }} 1x, {{ asset('img/comfaca-logo@2x.png') }} 2x" alt="Comfaca" />
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
                @if ($resultado['categoria'] !== '')
                    <p class="mb-2"><span class="verifica-badge">Categoría {{ $resultado['categoria'] }}</span></p>
                @endif
                <p class="verifica-meta mb-0">Verificado el {{ $resultado['fecha'] }}</p>

                <div class="verifica-nucleo">
                    @if ($resultado['beneficiarios'] === null)
                        <p class="verifica-meta mb-0 text-center">No fue posible consultar el núcleo familiar en este momento.</p>
                    @else
                        <div class="verifica-nucleo-title">Beneficiarios ({{ count($resultado['beneficiarios']) }})</div>
                        @if (count($resultado['beneficiarios']) > 0)
                            <ul class="verifica-beneficiarios">
                                @foreach ($resultado['beneficiarios'] as $beneficiario)
                                    <li>
                                        <span class="verifica-beneficiario-icon"><i class="fas {{ $beneficiario['icono'] }}"></i></span>
                                        <div class="verifica-beneficiario-info">
                                            <div class="verifica-beneficiario-nombre">{{ $beneficiario['nombre'] }}</div>
                                            <div class="verifica-meta">{{ $beneficiario['documento'] }}</div>
                                        </div>
                                        <span class="verifica-beneficiario-parentesco">{{ $beneficiario['parentesco'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="verifica-meta mb-0">Sin beneficiarios activos registrados.</p>
                        @endif
                    @endif
                </div>
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
