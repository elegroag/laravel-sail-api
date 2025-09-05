@foreach ($param->load_archivos as $ai => $archivo)
    @if ($archivo->diponible)
        <tr>
            <td colspan='2'>
                <button type='button' class='btn btn-sm btn-outline-primary' toggle-event='show' data-href='{{ $archivo->diponible }}'>
                    <span class='btn-inner--icon'><i class='fas fa-file-download'></i></span>
                    <span class='btn-inner--text'>{{ $archivo->detalle }}</span>
                </button>
                {!! ($archivo->corrige) ? '<span class="text-warning">Archivo por devolución</span>' : '' !!}
            </td>
            @if ($param->puede_borrar)
                <td>
                    <button class='btn btn-icon btn-danger btn-sm btn-outline-danger' type='button' toggle-event='borrar' data-id='{{ $archivo->id }}' data-coddoc='{{ $archivo->coddoc }}'>
                        <span class='btn-inner--icon'><i class='fas fa-save'></i> Borrar</span>
                    </button>
                </td>
            @else
                <td></td>
            @endif
        </tr>
    @else
        <tr>
            <td style='width:30%'>{{ $archivo->detalle }} {{ $archivo->obliga }}</td>
            <td>
                <div class='custom-file'>
                    <input type='file' class='custom-file-input' toggle-event='change' data-coddoc='{{ $archivo->coddoc }}' id='archivo_{{ $archivo->coddoc }}' name='archivo_{{ $archivo->coddoc }}' accept='application/pdf, image/*' />
                    <label class='custom-file-label toogle-show-name' for='customFileLang' data-code='{{ $archivo->coddoc }}'>Selecciona y carga aquí...</label>
                </div>
            </td>
            <td>
                <button class='btn btn-icon btn-primary btn-sm btn-outline-primary' type='button' toggle-event='salvar' data-id='{{ $archivo->id }}' data-coddoc='{{ $archivo->coddoc }}'>
                    <span class='btn-inner--icon'><i class='fas fa-save'></i> Guardar</span>
                </button>
            </td>
        </tr>
    @endif
@endforeach