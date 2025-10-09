@php
    // Definir opciones de paginación
    $perPageOptions = [5, 30, 50, 100];
    
    // Calcular rangos de páginas para mostrar
    $startPage = max($paginate->current - 5, $paginate->first);
    $endPage = min($paginate->current + 5, $paginate->last);
    
    // Determinar si los botones de navegación deben estar deshabilitados
    $isFirstPage = $paginate->current <= $paginate->first;
    $isLastPage = $paginate->current >= $paginate->last;
@endphp

<div class='row'>
    <!-- Selector de cantidad de registros por página -->
    <div class='col-sm-12 col-md-auto mr-auto pr-0 d-none d-md-inline'>
        <label class='text-nowrap mb-0'>
            Mostrar 
            <select 
                id='cantidad_paginate' 
                name='cantidad_paginate' 
                class='form-control form-control-sm d-sm-inline-block w-auto' 
                data-toggle='paginate-change'
            >
                @foreach($perPageOptions as $option)
                    <option value='{{ $option }}' {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            registros
        </label>
    </div>
    
    <!-- Controles de paginación -->
    <div class='col-sm-12 col-md-auto pl-0 pr-0 pr-sm-3'>
        <nav aria-label='Paginación'>
            <ul class='pagination justify-content-center justify-content-md-end mb-0'>
                <!-- Primera página -->
                <li 
                    class='page-item {{ $isFirstPage ? 'disabled' : '' }}' 
                    data-toggle='paginate-buscar' 
                    pagina='{{ $paginate->first }}' {{ $isFirstPage ? 'disabled' : '' }}>
                    <a class='page-link' {{ $isFirstPage ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                        <i class='fas fa-angle-double-left'></i>
                    </a>
                </li>
                
                <!-- Página anterior -->
                <li 
                    class='page-item {{ $isFirstPage ? 'disabled' : '' }}' 
                    data-toggle='paginate-buscar' 
                    pagina='{{ $paginate->before }}' {{ $isFirstPage ? 'disabled' : '' }}>
                    <a class='page-link' {{ $isFirstPage ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                        <i class='fas fa-angle-left'></i>
                    </a>
                </li>
                
                <!-- Rango de páginas -->
                @for($i = $startPage; $i <= $endPage; $i++)
                    @php
                        $isActive = $i == $paginate->current ? 'active' : '';
                    @endphp
                    <li class='page-item {{ $isActive }}' data-toggle='paginate-buscar' pagina='{{ $i }}'>
                        <a class='page-link'>{{ $i }}</a>
                    </li>
                @endfor
                
                <!-- Siguiente página -->
                <li 
                    class='page-item {{ $isLastPage ? 'disabled' : '' }}' 
                    data-toggle='paginate-buscar' 
                    pagina='{{ $paginate->next }}' {{ $isLastPage ? 'disabled' : '' }}>
                    <a class='page-link' {{ $isLastPage ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                        <i class='fas fa-angle-right'></i>
                    </a>
                </li>
                
                <!-- Última página -->
                <li 
                    class='page-item {{ $isLastPage ? 'disabled' : '' }}' 
                    data-toggle='paginate-buscar' 
                    pagina='{{ $paginate->last }}' {{ $isLastPage ? 'disabled' : '' }}>
                    <a class='page-link' {{ $isLastPage ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                        <i class='fas fa-angle-double-right'></i>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>