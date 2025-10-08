<div class="modal fade" id="filtrar-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding:10px;padding-right:20px">
                <h6 class="modal-title">Filtro</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:20px;">
                <div class="d-flex p-2">
                    <div class="col-sm-6 col-md-3" style="padding:5px;">
                        <div class="form-group">
                            <select class="form-control" style="padding:8px;height:35px" name="campo-filtro" id="campo-filtro">
                                @foreach ($campo_filtro as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3" style="padding:5px;">
                        <div class="form-group">
                            <select class="form-control" name="condi-filtro" id="condi-filtro" style="padding:8px;height:35px">
                                <option value="como">Coincidencia</option>
                                <option value="igual">Igual</option>
                                <option value="mayor">Mayor</option>
                                <option value="mayorigual">Mayor Igual</option>
                                <option value="menorigual">Menor Igual</option>
                                <option value="menor">Menor</option>
                                <option value="diferente">Diferente</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3" style="padding:5px;">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Valor filtro" style="padding:8px;height:35px" name="value-filtro" id="value-filtro" />
                        </div>
                    </div>
                    <div class="form-group" style="padding:5px;">
                        <button class='btn btn-success btn-sm' toggle-event='add_filtro'>
                            Adicionar
                        </button>
                        <button class='btn btn-danger btn-sm' toggle-event='borrar_filtro'>
                            Borrar
                        </button>
                    </div>
                </div>

                <div id="filtro_add">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Campo</th>
                                <th>Condición</th>
                                <th>Valor</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody id='tbody_filter'>
                            @if (isset($filters) && $filters != false)
                                @foreach ($filters['campo'] as $xk => $value)
                                    <tr>
                                        <td>
                                            {{ $filters['campo'][$xk]['mcampo'] }}
                                            <input id='mcampo-filtro[]' name='mcampo-filtro[]' type='hidden' value='{{ $filters['campo'][$xk]['mcampo'] }}' />
                                        </td>
                                        <td>
                                            {{ $filters['condi'][$xk]['mcondi'] }}
                                            <input id='mcondi-filtro[]' name='mcondi-filtro[]' type='hidden' value='{{ $filters['condi'][$xk]['mcondi'] }}' />
                                        </td>
                                        <td>
                                            {{ $filters['value'][$xk]['mvalue'] }}
                                            <input id='mvalue-filtro[]' name='mvalue-filtro[]' type='hidden' value='{{ $filters['value'][$xk]['mvalue'] }}' />
                                        </td>
                                        <td>
                                            <button class='btn btn-outline-danger btn-sm' toggle-event='remove' data-key='{{ $xk }}'>
                                                <span class='btn-inner--icon'><i class='fas fa-trash'></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class='btn btn-primary' toggle-event='aplicar_filtro'>
                    Aplicar
                </button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>