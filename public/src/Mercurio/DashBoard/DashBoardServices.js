import { Messages } from '@/Utils';

const getChartMajorVersion = () => {
    const raw = Chart?.version || '2.0.0';
    const major = parseInt(String(raw).split('.')[0], 10);
    return Number.isFinite(major) ? major : 2;
};

const toNumber = (value) => {
    if (value === null || value === undefined) return 0;
    if (typeof value === 'number') return value;
    const normalized = String(value).replace(/[^0-9.-]/g, '');
    const n = Number(normalized);
    return Number.isFinite(n) ? n : 0;
};

const getOrCreateChartStore = () => {
    if (!window.__dashboardCharts) window.__dashboardCharts = {};
    return window.__dashboardCharts;
};

const setChartState = (containerSelector, state) => {
    const $container = $(containerSelector);
    if ($container.length === 0) return;
    const $loading = $container.find('.chart-loading');
    const $empty = $container.find('.chart-empty');

    if (state === 'loading') {
        $loading.removeClass('d-none');
        $empty.addClass('d-none');
        return;
    }

    if (state === 'empty') {
        $loading.addClass('d-none');
        $empty.removeClass('d-none');
        return;
    }

    $loading.addClass('d-none');
    $empty.addClass('d-none');
};

const destroyChartIfExists = (key) => {
    const store = getOrCreateChartStore();
    if (store[key] && typeof store[key].destroy === 'function') {
        store[key].destroy();
    }
    store[key] = null;
};

const buildBarOptions = () => {
    const major = getChartMajorVersion();
    if (major >= 3) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        };
    }
    return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [
                {
                    ticks: {
                        beginAtZero: true,
                    },
                },
            ],
        },
    };
};

const TraerAportesEmpresa = () => {
    setChartState('#render_chart_aportes', 'loading');
    window.App.trigger('syncro', {
        url: window.App.url('principal/traer_aportes_empresa'),
        data: {},
        silent: false,
        callback: (response) => {
            if (response.success === true) {
                const canvas = document.getElementById('chart-aportes');
                if (!canvas) return;
                destroyChartIfExists('aportes');
                const ctx = canvas.getContext('2d');
                const store = getOrCreateChartStore();
                const values = (response.data || []).map(toNumber);
                if (values.length === 0) {
                    setChartState('#render_chart_aportes', 'empty');
                    return;
                }
                store.aportes = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [
                            {
                                data: values,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: buildBarOptions(),
                });
                setChartState('#render_chart_aportes', 'ready');
            } else {
                setChartState('#render_chart_aportes', 'empty');
                Messages.display(response.message, 'error');
            }
        },
    });
};

const TraerCategoriasEmpresa = () => {
    setChartState('#render_chart_categorias', 'loading');
    window.App.trigger('syncro', {
        url: window.App.url('principal/traer_categorias_empresa'),
        data: {},
        silent: false,
        callback: (response) => {
            if (response.success === true) {
                const canvas = document.getElementById('chart-categorias');
                if (!canvas) return;
                destroyChartIfExists('categorias');
                const ctx = canvas.getContext('2d');
                const store = getOrCreateChartStore();
                const values = (response.data || []).map(toNumber);
                if (values.length === 0) {
                    setChartState('#render_chart_categorias', 'empty');
                    return;
                }
                store.categorias = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: response.labels,
                        datasets: [
                            {
                                data: values,
                                backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                    },
                });
                setChartState('#render_chart_categorias', 'ready');
            } else {
                setChartState('#render_chart_categorias', 'empty');
                Messages.display(response.message, 'error');
            }
        },
    });
};

const TraerGiroEmpresa = () => {
    setChartState('#render_chart_giro', 'loading');
    window.App.trigger('syncro', {
        url: window.App.url('principal/traer_giro_empresa'),
        data: {},
        silent: false,
        callback: (response) => {
            if (response.success === true) {
                const canvas = document.getElementById('chart-giro');
                if (!canvas) return;
                destroyChartIfExists('giro');
                const ctx = canvas.getContext('2d');
                const store = getOrCreateChartStore();
                const values = (response.data || []).map(toNumber);
                if (values.length === 0) {
                    setChartState('#render_chart_giro', 'empty');
                    return;
                }
                store.giro = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: response.labels,
                        datasets: [
                            {
                                data: values,
                                backgroundColor: 'rgba(255, 205, 86, 0.6)',
                                borderColor: 'rgba(255, 205, 86, 1)',
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: buildBarOptions(),
                });
                setChartState('#render_chart_giro', 'ready');
            } else {
                setChartState('#render_chart_giro', 'empty');
                Messages.display(response.message, 'error');
            }
        },
    });
};

export { TraerAportesEmpresa, TraerCategoriasEmpresa, TraerGiroEmpresa };
