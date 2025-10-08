<div class='row'>
    <div class='col-sm-12 col-md-auto mr-auto pr-0 d-none d-md-inline'>
        <label class='text-nowrap mb-0'> Mostrar
            <select
                id='cantidad_paginate'
                name='cantidad_paginate'
                class='form-control form-control-sm d-sm-inline-block w-auto'
                toggle-event='change'>
                <option value=''>Seleccionar aquí</option>
                @foreach([10, 20, 30, 50, 100] as $num)
                    <option value='{{ $num }}'>{{ $num }}</option>
                @endforeach
            </select> registros
        </label>
    </div>
    <div class='col-sm-12 col-md-auto pl-0 pr-0 pr-sm-3'>
        <nav aria-label='...'>
            <ul class='pagination justify-content-center justify-content-md-end mb-0'>

                <li class='page-item' toggle-event='buscar' pagina='{{ $paginate->first }}'>
                    <a class='page-link'>
                        <i class='fas fa-angle-double-left'></i>
                    </a>
                </li>

                <li class='page-item' toggle-event='buscar' pagina='{{ $paginate->before }}'>
                    <a class='page-link'><i class='fas fa-angle-left'></i></a>
                </li>
                @for ($i = $paginate->current - 5; $i < $paginate->current; $i++)
                    @if ($i < $paginate->first) @continue
                    @endif
                    <li class='page-item' toggle-event='buscar'>
                        <a class='page-link'>{{ $i }}</a>
                    </li>
                @endfor

                @for ($i = $paginate->current; $i <= ($paginate->current + 5); $i++)
                    @if ($i > $paginate->last) @continue
                    @endif
                    <li class="page-item {{ ($i == $paginate->current) ? "active" : "" }}" toggle-event='buscar'>
                        <a class='page-link'>{{ $i }}</a>
                    </li>
                @endfor

                <li class='page-item' toggle-event='buscar' pagina='{{ $paginate->next }}'>
                    <a class='page-link'><i class='fas fa-angle-right'></i></a>
                </li>

                <li class='page-item' toggle-event='buscar' pagina='{{ $paginate->last }}'>
                    <a class='page-link'><i class='fas fa-angle-double-right'></i></a>
                </li>
            </ul>
        </nav>
    </div>
</div>