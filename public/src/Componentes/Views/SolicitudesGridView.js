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
        const { onReload } = options;

        if (!$search.length || !$grid.length) {
            return;
        }

        const state = {
            searchTerm: '',
            currentPage: 1,
            pageSize: parseInt($pageSize.val(), 10) || 10,
        };

        const applyState = () => {
            const cards = $grid.find('.solicitud-card').toArray();
            const matched = cards.filter((card) => {
                const haystack = normalizeSearch($(card).attr('data-search') || '');
                return state.searchTerm === '' || haystack.includes(state.searchTerm);
            });

            const totalMatched = matched.length;
            const totalExisting = cards.length;
            const totalPages = totalMatched > 0 ? Math.ceil(totalMatched / state.pageSize) : 0;

            if (totalPages > 0 && state.currentPage > totalPages) {
                state.currentPage = totalPages;
            }
            if (totalPages === 0) {
                state.currentPage = 1;
            }

            const start = (state.currentPage - 1) * state.pageSize;
            const end = start + state.pageSize;
            const visibleSet = new Set(matched.slice(start, end));
            const visibleCount = visibleSet.size;

            cards.forEach((card) => {
                $(card).toggleClass('d-none', !visibleSet.has(card));
            });

            const hasCards = cards.length > 0;
            const showEmpty = hasCards && totalMatched === 0;
            $empty.toggleClass('d-none', !showEmpty);

            if (totalPages === 0) {
                $pageInfo.text('0 / 0');
            } else {
                $pageInfo.text(`${state.currentPage} / ${totalPages}`);
            }

            if (totalMatched === 0) {
                $summary.text(`Mostrando 0 registros · Total existentes: ${totalExisting}`);
            } else {
                const visibleStart = start + 1;
                const visibleEnd = start + visibleCount;
                $summary.text(`Mostrando ${visibleStart}-${visibleEnd} de ${totalMatched} registros · Total existentes: ${totalExisting}`);
            }

            $prevBtn.prop('disabled', state.currentPage <= 1 || totalPages === 0);
            $nextBtn.prop('disabled', state.currentPage >= totalPages || totalPages === 0);
        };

        const runSearch = () => {
            state.searchTerm = normalizeSearch($search.val());
            state.currentPage = 1;
            applyState();
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
            if (typeof onReload === 'function') {
                $search.val('');
                onReload();
                return;
            }
            $search.val('');
            state.searchTerm = '';
            state.currentPage = 1;
            state.pageSize = parseInt($pageSize.val(), 10) || 10;
            applyState();
        });

        $pageSize.off(`change${ns}`).on(`change${ns}`, () => {
            state.pageSize = parseInt($pageSize.val(), 10) || 10;
            state.currentPage = 1;
            applyState();
        });

        $prevBtn.off(`click${ns}`).on(`click${ns}`, () => {
            if (state.currentPage > 1) {
                state.currentPage -= 1;
                applyState();
            }
        });

        $nextBtn.off(`click${ns}`).on(`click${ns}`, () => {
            state.currentPage += 1;
            applyState();
        });

        applyState();
    },

    initSearch($container, options = {}) {
        SolicitudesGridView.init($container, options);
    },
};

export { SolicitudesGridView };
