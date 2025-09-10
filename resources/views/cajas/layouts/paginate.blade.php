<div class='row'>
    <div class='col-sm-12 col-md-auto mr-auto pr-0 d-none d-md-inline'>
        <label class='text-nowrap mb-0'> Mostrar
            <select
                id='cantidad_paginate'
                name='cantidad_paginate'
                class='form-control form-control-sm d-sm-inline-block w-auto'
                toggle-event='change'>
                <option value=''>Seleccionar aquí</option>
                <option <?= ($numero == 10) ? 'selected' : '' ?> value='10'>10</option>
                <option <?= ($numero == 20) ? 'selected' : '' ?> value='20'>20</option>
                <option <?= ($numero == 30) ? 'selected' : '' ?> value='30'>30</option>
                <option <?= ($numero == 50) ? 'selected' : '' ?> value='50'>50</option>
                <option <?= ($numero == 100) ? 'selected' : '' ?> value='100'>100</option>
            </select> registros
        </label>
    </div>
    <div class='col-sm-12 col-md-auto pl-0 pr-0 pr-sm-3'>
        <nav aria-label='...'>
            <ul class='pagination justify-content-center justify-content-md-end mb-0'>

                <li class='page-item' toggle-event='buscar' pagina='<?= $paginate->first ?>'>
                    <a class='page-link'>
                        <i class='fas fa-angle-double-left'></i>
                    </a>
                </li>

                <li class='page-item' toggle-event='buscar' pagina='<?= $paginate->before ?>'>
                    <a class='page-link'><i class='fas fa-angle-left'></i></a>
                </li>
                <?
                for ($i = $paginate->current - 5; $i < $paginate->current; $i++) {
                    if ($i < $paginate->first) continue;
                ?>
                    <li class='page-item' toggle-event='buscar'>
                        <a class='page-link'><?= $i ?></a>
                    </li>
                <? } ?>

                <?
                for ($i = $paginate->current; $i <= ($paginate->current + 5); $i++) {
                    $clase = '';
                    if ($i == $paginate->current) $clase = "active";
                    if ($i > $paginate->last) continue;
                ?>
                    <li class="page-item <?= $clase ?>" toggle-event='buscar'>
                        <a class='page-link'><?= $i ?></a>
                    </li>
                <? } ?>
                <li class='page-item' toggle-event='buscar' pagina='<?= $paginate->next ?>'>
                    <a class='page-link'><i class='fas fa-angle-right'></i></a>
                </li>
                <li class='page-item' toggle-event='buscar' pagina='<?= $paginate->last ?>'>
                    <a class='page-link'><i class='fas fa-angle-double-right'></i></a>
                </li>
            </ul>
        </nav>
    </div>
</div>