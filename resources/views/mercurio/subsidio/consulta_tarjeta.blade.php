@extends('layouts.bone')

@section('content')
<div class="col-12 mt-3">
    <div class="nav-wrapper">
        <ul class="nav nav-pills" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-bs-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Saldo Pendiente por Cobrar
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="row">
                        <table class="table align-items-center table-bordered">
                            <thead class="bg-green-blue">
                                <tr>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Quien recibe cuota</th>
                                    <th scope="col">Parentesco</th>
                                    <th scope="col">Giro</th>
                                    <th scope="col">Abonos (Fecha - Valor - Periodo Giro)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($saldos as $msaldo)
                                    @php($giro = $msaldo['giro'] === 'S' ? 'SI' : ($msaldo['giro'] === 'N' ? 'NO' : ''))
                                    <tr>
                                        <td>{{ $msaldo['documento'] }}</td>
                                        <td>{{ $msaldo['prinom'] }} {{ $msaldo['segnom'] }} {{ $msaldo['priape'] }} {{ $msaldo['segape'] }}</td>
                                        <td>{{ $msaldo['parent'] }}</td>
                                        <td>{{ $giro }}</td>
                                        <td>
                                            @if (!empty($msaldo['abonos']))
                                                <table class="table table-hover align-items-center table-bordered">
                                                    <tbody>
                                                        @foreach ($msaldo['abonos'] as $mabono)
                                                            @php($saldo_pendiente += $mabono['valor_abono'])
                                                            @php($valor_abono = number_format($mabono['valor_abono'], 2, ',', '.'))
                                                            <tr>
                                                                <td style="width: 50%">{{ $mabono['fecha'] }}</td>
                                                                <td style="width: 25%">$ {{ $valor_abono }}</td>
                                                                <td style="width: 25%">{{ $mabono['periodo_giro'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr align="center">
                                        <td colspan="5">No hay datos para mostrar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <hr />
                        <div class="row">
                            <div class="col-6">
                                <p>Saldo pendiente por cobrar: $ {{ number_format($saldo_pendiente, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('mercurio/build/ConsultasTrabajador.js') }}"></script>
@endpush
