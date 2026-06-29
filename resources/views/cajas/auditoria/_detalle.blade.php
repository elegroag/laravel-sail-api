<div class="p-3">
    <h6 class="heading-small text-muted mb-3">Resumen de la solicitud</h6>
    <table class="table table-sm table-bordered mb-0">
        <tbody>
            @foreach ($fields as $label => $value)
                <tr>
                    <th class="text-muted" style="width: 35%;">{{ $label }}</th>
                    <td>{{ $value ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (!empty($extraFields))
        <h6 class="heading-small text-muted mb-3 mt-4">Datos de la solicitud</h6>
        <table class="table table-sm table-bordered mb-0">
            <tbody>
                @foreach ($extraFields as $label => $value)
                    <tr>
                        <th class="text-muted" style="width: 35%;">{{ $label }}</th>
                        <td>{{ $value ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h6 class="heading-small text-muted mb-3 mt-4">Seguimiento</h6>
    {!! $seguimientoHtml !!}
</div>
