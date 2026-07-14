const normalizeSearch = (value = '') =>
    value
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

const SolicitudesGridView = {
    init($container, options = {}) {
        const $root = $container && $container.jquery ? $container : $($container);
        const $search = $root.find('#solicitudes-search');
        const $searchBtn = $root.find('#solicitudes-search-btn');
        const $reloadBtn = $root.find('#solicitudes-reload');
        const $pageSize = $root.find('#solicitudes-page-size');
        const $prevBtn = $root.find('#solicitudes-prev');
        const $nextBtn = $root.find('#solicitudes-next');
        const $pageInfo = $root.find('#solicitudes-page-info');
        const $grid = $root.find('#consulta');
        const $empty = $root.find('#solicitudes-empty');
        const $summary = $root.find('#solicitudes-summary');
        const { onReload, meta } = options;

        if (!$search.length || !$grid.length) {
            return;
        }

        const state = $root.data('solicitudesGridState') || {
            searchTerm: '',
            currentPage: 1,
            pageSize: parseInt($pageSize.val(), 10) || 10,
            total: 0,
            totalPages: 0,
            from: 0,
            to: 0,
        };

        if (meta) {
            state.currentPage = meta.current || 1;
            state.pageSize = meta.per_page || state.pageSize;
            state.total = meta.total || 0;
            state.totalPages = meta.total_pages || 0;
            state.from = meta.from || 0;
            state.to = meta.to || 0;
            $pageSize.val(String(state.pageSize));
        }

        $root.data('solicitudesGridState', state);

        const applyClientSearch = () => {
            // El backend puede inyectar dentro de #consulta un bloque
            // `.solicitudes-grid__empty` cuando no hay datos. Como la única fuente
            // canónica del mensaje vacío es el shell (#solicitudes-empty), lo
            // removemos siempre del DOM para evitar la duplicación.
            $grid.find('.solicitudes-grid__empty').remove();

            const cards = $grid.find('.solicitud-card').toArray();
            const matched = cards.filter((card) => {
                const haystack = normalizeSearch($(card).attr('data-search') || '');
                return state.searchTerm === '' || haystack.includes(state.searchTerm);
            });

            cards.forEach((card) => {
                $(card).toggleClass('d-none', !matched.includes(card));
            });

            const hasCards = cards.length > 0;
            // Caso A: el servidor no devolvió solicitudes (total = 0).
            // Caso B: hay tarjetas pero la búsqueda no encontró coincidencias.
            // En ambos casos mostramos el mensaje vacío del shell.
            const showEmpty =
                state.total === 0 ||
                (hasCards && matched.length === 0 && state.searchTerm !== '');
            $empty.toggleClass('d-none', !showEmpty);
        };

        const updatePaginationUi = () => {
            if (state.totalPages === 0) {
                $pageInfo.text('0 / 0');
            } else {
                $pageInfo.text(`${state.currentPage} / ${state.totalPages}`);
            }

            if (state.total === 0) {
                $summary.text('Mostrando 0 registros · Total existentes: 0');
            } else if (state.from > 0 && state.to > 0) {
                $summary.text(`Mostrando ${state.from}-${state.to} de ${state.total} registros · Total existentes: ${state.total}`);
            } else {
                $summary.text(`Mostrando ${state.total} registros · Total existentes: ${state.total}`);
            }

            $prevBtn.prop('disabled', state.currentPage <= 1 || state.totalPages === 0);
            $nextBtn.prop('disabled', state.currentPage >= state.totalPages || state.totalPages === 0);
        };

        const runSearch = () => {
            state.searchTerm = normalizeSearch($search.val());
            applyClientSearch();
        };

        const reloadGrid = (page = state.currentPage, perPage = state.pageSize) => {
            if (typeof onReload !== 'function') {
                return;
            }

            onReload({
                page,
                perPage,
            });
        };

        const ns = '.solicitudesGrid';

        $searchBtn.off(`click${ns}`).on(`click${ns}`, runSearch);
        $search.off(`keydown${ns}`).on(`keydown${ns}`, (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                runSearch();
            }
        });

        $reloadBtn.off(`click${ns}`).on(`click${ns}`, () => {
            $search.val('');
            state.searchTerm = '';
            reloadGrid(state.currentPage, state.pageSize);
        });

        $pageSize.off(`change${ns}`).on(`change${ns}`, () => {
            state.pageSize = parseInt($pageSize.val(), 10) || 10;
            reloadGrid(1, state.pageSize);
        });

        $prevBtn.off(`click${ns}`).on(`click${ns}`, () => {
            if (state.currentPage > 1) {
                reloadGrid(state.currentPage - 1, state.pageSize);
            }
        });

        $nextBtn.off(`click${ns}`).on(`click${ns}`, () => {
            if (state.totalPages > 0 && state.currentPage < state.totalPages) {
                reloadGrid(state.currentPage + 1, state.pageSize);
            }
        });

        updatePaginationUi();
        applyClientSearch();
    },

    initSearch($container, options = {}) {
        SolicitudesGridView.init($container, options);
    },
};

export { SolicitudesGridView };
