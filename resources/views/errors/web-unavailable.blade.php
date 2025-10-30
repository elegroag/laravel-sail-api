@extends('layouts.auth')

@section('title', 'No disponible | 404')
@section('application', 'web')

@push('styles')
    <style>
        .error-card {
            max-width: 560px;
            width: 100%;
        }
        .error-code {
            font-size: 72px;
            font-weight: 800;
            line-height: 1;
        }
        .muted { color: #6c757d; }
    </style>
@endpush

@section('content')
<script>
    console.log("Ruta error: {{ $ruta ?? request()->url() }}");
</script>
<div class="container py-4">
    <div class="card shadow error-card mx-auto">
        <div class="card-body text-center p-5">
            <div class="error-code text-primary mb-3">404</div>
            <h1 class="h3 mb-3">Ruta no disponible en la web</h1>
            <p class="mb-4 muted">
                La ruta no está disponible para acceder desde la web.
            </p>
            
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('login') }}" class="btn btn-primary border-0">
                    Ir al inicio de sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
