@php
    use App\Services\Ecommerce\ConciliacionServiciosService;

    $cards = [
        ConciliacionServiciosService::CONCILIADA => ['class' => 'border-success', 'text' => 'text-success'],
        ConciliacionServiciosService::PAGADA_SIN_VENTA => ['class' => 'border-danger', 'text' => 'text-danger'],
        ConciliacionServiciosService::VALOR_DIFERENTE => ['class' => 'border-warning', 'text' => 'text-warning'],
        ConciliacionServiciosService::VENTA_SIN_PRECOMPRA_PA => ['class' => 'border-secondary', 'text' => 'text-dark'],
        ConciliacionServiciosService::ACEPTADA_EPAYCO_NO_PA => ['class' => 'border-primary', 'text' => 'text-primary'],
        ConciliacionServiciosService::SIN_TX_EPAYCO => ['class' => 'border-info', 'text' => 'text-info'],
        ConciliacionServiciosService::VENTA_NO_APROBADA => ['class' => 'border-dark', 'text' => 'text-dark'],
    ];
@endphp

<div class="d-flex flex-wrap gap-2">
    @foreach (ConciliacionServiciosService::ESTADOS as $codigo => $label)
        @php $style = $cards[$codigo] ?? ['class' => '', 'text' => '']; @endphp
        <div class="card {{ $style['class'] }} shadow-none">
            <div class="card-body py-2 px-3">
                <div class="small text-muted">{{ $label }}</div>
                <div class="fs-5 fw-bold {{ $style['text'] }}">{{ (int) ($resumen[$codigo] ?? 0) }}</div>
            </div>
        </div>
    @endforeach
</div>
