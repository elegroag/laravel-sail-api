@extends('layouts.bone')

@push('styles')
    <style>
        .consulta-page-card {
            border-radius: 16px;
            overflow: hidden;
        }

        .consulta-page-header {
            padding: 1.25rem 1.5rem 0.5rem;
            border-bottom: 1px solid #eef2f7;
        }

        .consulta-page-title {
            margin: 0 0 0.35rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #334155;
        }

        .consulta-page-subtitle {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.55;
        }

        .consulta-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .consulta-summary-card {
            background: linear-gradient(145deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid rgba(13, 110, 253, 0.12);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .consulta-summary-card-accent {
            background: linear-gradient(145deg, #e7f1ff 0%, #ffffff 100%);
            border-color: rgba(13, 110, 253, 0.22);
        }

        .consulta-summary-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .consulta-summary-value {
            display: block;
            font-size: 1.35rem;
            line-height: 1.2;
            color: #212529;
            font-weight: 700;
        }

        .consulta-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 2.5rem 1rem;
            border: 1px dashed #ced4da;
            border-radius: 14px;
            background: #f8f9fa;
            color: #6c757d;
            text-align: center;
        }

        .consulta-empty-state i {
            font-size: 1.75rem;
            color: #adb5bd;
        }

        .consulta-table-wrap {
            border: 1px solid #e9ecef;
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .consulta-data-table {
            font-size: 0.84rem;
            margin-bottom: 0;
        }

        .consulta-data-table thead th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 0;
            padding: 0.75rem 0.85rem;
            white-space: nowrap;
            vertical-align: middle;
        }

        .consulta-data-table tbody td {
            padding: 0.75rem 0.85rem;
            vertical-align: top;
            color: #334155;
            border-color: #eef2f7;
        }

        .consulta-data-table tbody tr:hover {
            background: #f8fbff;
        }

        .consulta-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #eef2f7;
            color: #475569;
        }

        .consulta-badge--success {
            background: #d1fae5;
            color: #047857;
        }

        .consulta-badge--muted {
            background: #fee2e2;
            color: #b91c1c;
        }

        .consulta-abonos-table {
            width: 100%;
            font-size: 0.8rem;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .consulta-abonos-table td {
            padding: 0.45rem 0.6rem;
            border-bottom: 1px solid #eef2f7;
        }

        .consulta-abonos-table tr:last-child td {
            border-bottom: 0;
        }
    </style>
@endpush

@section('content')
<div class="col-12 col-xl-10 mx-auto mt-3">
    <div class="card mb-0 shadow-sm border-0 consulta-page-card">
        <div class="card-header border-0 consulta-page-header">
            <div>
                <h2 class="consulta-page-title">{{ $title ?? 'Saldo pendiente' }}</h2>
                <p class="consulta-page-subtitle">
                    Consulta los abonos pendientes por cobrar asociados a tu cuota monetaria.
                </p>
            </div>
        </div>

        <div class="card-body px-3 px-md-4 py-4">
            @php
                $totalPendiente = 0;
                if (!empty($saldos) && is_iterable($saldos)) {
                    foreach ($saldos as $msaldo) {
                        if (!empty($msaldo['abonos']) && is_iterable($msaldo['abonos'])) {
                            foreach ($msaldo['abonos'] as $mabono) {
                                $totalPendiente += (float) ($mabono['valor_abono'] ?? 0);
                            }
                        }
                    }
                }
            @endphp

            <div class="consulta-summary-grid">
                <div class="consulta-summary-card consulta-summary-card-accent">
                    <span class="consulta-summary-label">Saldo pendiente por cobrar</span>
                    <span class="consulta-summary-value">$ {{ number_format($totalPendiente, 2, ',', '.') }}</span>
                </div>
            </div>

            @if (empty($saldos) || !is_iterable($saldos) || count($saldos) === 0)
                <div class="consulta-empty-state">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <p class="mb-0">No hay datos para mostrar</p>
                </div>
            @else
                <div class="consulta-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-hover consulta-data-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Quien recibe cuota</th>
                                    <th scope="col">Parentesco</th>
                                    <th scope="col">Giro</th>
                                    <th scope="col">Abonos (fecha - valor - periodo giro)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($saldos as $msaldo)
                                    @php
                                        $giro = $msaldo['giro'] === 'S' ? 'SI' : ($msaldo['giro'] === 'N' ? 'NO' : '');
                                        $nombreCompleto = trim(
                                            ($msaldo['prinom'] ?? '') . ' ' .
                                            ($msaldo['segnom'] ?? '') . ' ' .
                                            ($msaldo['priape'] ?? '') . ' ' .
                                            ($msaldo['segape'] ?? '')
                                        );
                                    @endphp
                                    <tr>
                                        <td>{{ $msaldo['documento'] ?? '—' }}</td>
                                        <td>{{ $nombreCompleto ?: '—' }}</td>
                                        <td>{{ $msaldo['parent'] ?? '—' }}</td>
                                        <td>
                                            @if ($giro === 'SI')
                                                <span class="consulta-badge consulta-badge--success">Sí</span>
                                            @elseif ($giro === 'NO')
                                                <span class="consulta-badge consulta-badge--muted">No</span>
                                            @else
                                                <span class="consulta-badge">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($msaldo['abonos']))
                                                <table class="consulta-abonos-table">
                                                    <tbody>
                                                        @foreach ($msaldo['abonos'] as $mabono)
                                                            @php
                                                                $valorAbono = number_format((float) ($mabono['valor_abono'] ?? 0), 2, ',', '.');
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $mabono['fecha'] ?? '—' }}</td>
                                                                <td>$ {{ $valorAbono }}</td>
                                                                <td>{{ $mabono['periodo_giro'] ?? '—' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <span class="text-muted">Sin abonos</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ versioned_asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush
