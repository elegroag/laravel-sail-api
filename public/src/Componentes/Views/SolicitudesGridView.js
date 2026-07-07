const normalizeSearch = (value = '') =>
    value
        .toString()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

const SolicitudesGridView = {
    initSearch($container) {
        const $root = $container && $container.jquery ? $container : $($container);
        const $search = $root.find('#solicitudes-search');
        const $grid = $root.find('#consulta');
        const $empty = $root.find('#solicitudes-empty');

        if (!$search.length || !$grid.length) {
            return;
        }

        const filterCards = () => {
            const term = normalizeSearch($search.val());
            let visibleCount = 0;

            $grid.find('.solicitud-card').each((_, card) => {
                const $card = $(card);
                const haystack = normalizeSearch($card.attr('data-search') || '');
                const matches = term === '' || haystack.includes(term);
                $card.toggleClass('d-none', !matches);
                if (matches) {
                    visibleCount += 1;
                }
            });

            const hasCards = $grid.find('.solicitud-card').length > 0;
            const showEmpty = hasCards && visibleCount === 0;
            $empty.toggleClass('d-none', !showEmpty);
        };

        $search.off('input.solicitudesGrid').on('input.solicitudesGrid', filterCards);
        filterCards();
    },
};

export { SolicitudesGridView };
