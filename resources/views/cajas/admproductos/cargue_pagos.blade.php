@extends('layouts.cajas')

@section('content')
    @include('cajas/templates/tmp_header_adapter', [
        'sub_title' => $title ?? 'Cargue De Pagos',
        'filtrar' => false,
        'listar' => false,
        'salir' => true,
        'add' => false,
    ])

    <div class="container-fluid mt--9 pb-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-green-blue p-1">
                        <a href="{{ route('admproductos.lista') }}" class="btn btn-md btn-primary">
                            <i class="fas fa-home"></i> Salir
                        </a>
                    </div>
                    <div class="card-body p-3">
                        <h3 class="mb-1">Cargue De Pagos</h3>
                        @if ($servicio)
                            <p class="mb-3">{{ $servicio->getServicio() }} ({{ $codser }})</p>
                        @endif
                        <p class="text-muted mb-0">
                            Afiliados aplicados al servicio: {{ is_countable($aplicados) ? count($aplicados) : 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
