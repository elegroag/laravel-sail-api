@if ($items->isEmpty())
    <div class="historial-empty-state">
        <i class="fas fa-inbox" aria-hidden="true"></i>
        <p class="mb-0">No hay datos para mostrar</p>
    </div>
@else
    <div class="historial-table-wrap">
        <div class="table-responsive">
            <table class="table table-hover historial-data-table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Cédula</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Fecha estado</th>
                        <th scope="col">Motivo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $row)
                        <tr>
                            <td>{{ $row->cedcon }}</td>
                            <td>{{ trim($row->priape . ' ' . $row->prinom) }}</td>
                            <td><span class="historial-status-badge">{{ $row->estadoDetalle }}</span></td>
                            <td>{{ $row->fecest ?: '—' }}</td>
                            <td>{{ $row->motivo ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
