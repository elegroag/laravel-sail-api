<div class="card-body">
    @if ($tipo == "S") <h4>Permite</h4> @endif
    @if ($tipo == "N") <h4>No permite</h4> @endif
    <ul class="list-group m-0 p-0">
        <li class="list-group-item">
            <div class="custom-control custom-checkbox">
                <input
                    type="checkbox"
                    id="selectall{{ $tipo }}"
                    name="selectall{{ $tipo }}"
                    autocomplete="off"
                    class="custom-control-input"
                    data-tipo="{{ $tipo }}"
                    toggle-event="eventCheckBox">
                <label class="custom-control-label" for="selectall{{ $tipo }}"> Seleccionar todo </label>
            </div>
        </li>
        <li class="list-group-item">
            <input type="text" class='form-control' id="buscar{{ $tipo }}" placeholder="Buscar" />
        </li>
    </ul>
    <ul class="list-group" id='perx{{ $tipo }}'>
        @foreach ($table as $mtable)
            <li class="list-group-item">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="{{ $mtable->getCodigo() }}">
                    <label class="custom-control-label" for="{{ $mtable->getCodigo() }}">{{ $mtable->getDetalle() }}</label>
                </div>
            </li>
        @endforeach
    </ul>
</div>