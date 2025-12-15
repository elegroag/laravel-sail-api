import { $App } from '@/App';
import ErrorHandler from '@/Common/ErrorHandler';
import Logger from '@/Common/Logger';
import { Region } from '@/Common/Region';
import MoraLayout from './views/MoraLayout';
import MoraPresuntaView from './views/MoraPresuntaView';

class MoraFiltrosView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.sucursales = options.sucursales || [];
        this.periodos = options.periodos || [];
        this.selectedSucursal = options.selectedSucursal || '';
        this.selectedPeriodo = options.selectedPeriodo || '';
    }

    render() {
        const sucursalOptions = _.map(this.sucursales, (s) => {
            const selected = `${s.codsuc}` === `${this.selectedSucursal}` ? 'selected' : '';
            return `<option value="${s.codsuc}" ${selected}>${s.codsuc}</option>`;
        }).join('');

        const periodoOptions = _.map(this.periodos, (p) => {
            const selected = `${p}` === `${this.selectedPeriodo}` ? 'selected' : '';
            return `<option value="${p}" ${selected}>${p}</option>`;
        }).join('');

        this.$el.html(`
			<form id="mora_filtros_form">
				<div class="mb-2">
					<label class="form-label mb-1" for="mora_sucursal_select">Sucursal</label>
					<select class="form-select form-select-sm" id="mora_sucursal_select" name="sucursal">
						${sucursalOptions}
					</select>
				</div>
				<div class="mb-2">
					<label class="form-label mb-1" for="mora_periodo_select">Periodo</label>
					<select class="form-select form-select-sm" id="mora_periodo_select" name="periodo" ${this.periodos.length === 0 ? 'disabled' : ''}>
						${periodoOptions}
					</select>
				</div>
				<button type="submit" class="btn btn-primary btn-sm w-100">Buscar</button>
			</form>
		`);
        return this;
    }

    get events() {
        return {
            'change #mora_sucursal_select': 'onChangeSucursal',
            'submit #mora_filtros_form': 'onSubmit',
        };
    }

    onChangeSucursal(e) {
        e.preventDefault();
        const sucursalId = this.$('#mora_sucursal_select').val();
        this.trigger('change:sucursal', sucursalId);
    }

    onSubmit(e) {
        e.preventDefault();
        const sucursalId = this.$('#mora_sucursal_select').val();
        const periodo = this.$('#mora_periodo_select').val();
        this.trigger('search', { sucursalId, periodo });
    }

    updatePeriodos(periodos = [], selectedPeriodo = '') {
        this.periodos = periodos;
        this.selectedPeriodo = selectedPeriodo;
        const periodoOptions = _.map(this.periodos, (p) => {
            const selected = `${p}` === `${this.selectedPeriodo}` ? 'selected' : '';
            return `<option value="${p}" ${selected}>${p}</option>`;
        }).join('');
        const $select = this.$('#mora_periodo_select');
        if ($select.length) {
            $select.html(periodoOptions);
            $select.prop('disabled', this.periodos.length === 0);
        }
    }
}

class MoraPresuntaApp {
    #dataManager;
    #errorHandler;
    #viewMora;
    #logger;
    #layout;
    #sucursalId;
    #periodo;
    #filtrosView;
    #hasSearch = false;

    constructor(options = {}) {
        _.extend(this, Backbone.Events);
        this.#errorHandler = new ErrorHandler();
        this.#logger = new Logger();
        this.#layout = new MoraLayout();
        this.App = options.App || window.App;
    }

    setDataManager(dataManager) {
        this.#dataManager = dataManager;
    }

    execute(sucursalId = null, periodo = null) {
        if (sucursalId) this.#sucursalId = sucursalId;
        if (periodo) this.#periodo = periodo;
        this.#hasSearch = Boolean(sucursalId && periodo);
        this.#initialize();
    }

    #initialize() {
        try {
            this.#initializeView();
        } catch (error) {
            this.#handleError(error, 'Error al inicializar la aplicación');
        }
    }

    #initializeView() {
        const region = new Region({ el: '#boneLayout' });
        region.show(this.#layout);

        this.#renderFiltros();
        if (this.#hasSearch) {
            this.#showTable();
            this.#updateView();
            this.#updateTitle();
        } else {
            this.#hideTable();
        }
    }

    #updateView() {
        if (!this.#hasSearch) return;
        if (this.#viewMora) this.#viewMora.remove();
        const cartera = this.#dataManager?.cartera?.[this.#sucursalId]?.[this.#periodo] ?? null;
        this.#viewMora = new MoraPresuntaView({
            model: {
                codsuc: this.#sucursalId,
                cartera: cartera,
                periodos: this.#dataManager.periodos,
                sucursales: this.#dataManager.sucursales,
            },
            errorHandler: this.#errorHandler,
        });
        this.#layout.getRegion('table').show(this.#viewMora);
    }

    #renderFiltros() {
        const sucursales = this.#dataManager?.sucursales || [];
        if (!this.#sucursalId && sucursales.length > 0) {
            this.#sucursalId = sucursales[0].codsuc;
        }

        const periodos = this.#getPeriodosBySucursal(this.#sucursalId);
        if (!this.#periodo && periodos.length > 0) {
            this.#periodo = periodos[0];
        }
        if (this.#periodo && periodos.length > 0 && !periodos.includes(this.#periodo)) {
            this.#periodo = periodos[0];
        }

        this.#filtrosView = new MoraFiltrosView({
            sucursales,
            periodos,
            selectedSucursal: this.#sucursalId,
            selectedPeriodo: this.#periodo,
        });

        this.listenTo(this.#filtrosView, 'change:sucursal', (sucursalId) => {
            this.#sucursalId = sucursalId;
            const nuevosPeriodos = this.#getPeriodosBySucursal(this.#sucursalId);
            const nuevoPeriodo = nuevosPeriodos.length > 0 ? nuevosPeriodos[0] : '';
            this.#periodo = nuevoPeriodo;
            this.#filtrosView.updatePeriodos(nuevosPeriodos, nuevoPeriodo);
        });

        this.listenTo(this.#filtrosView, 'search', ({ sucursalId, periodo }) => {
            this.#sucursalId = sucursalId;
            this.#periodo = periodo;
            this.#hasSearch = true;
            this.#showTable();
            this.#updateView();
            this.#updateTitle();
            setTimeout(() => {
                $App.router.navigate(`list/${this.#sucursalId}/${this.#periodo}`, {
                    trigger: false,
                    replace: true,
                });
            }, 0);
        });

        this.#layout.getRegion('periodos').show(this.#filtrosView);
    }

    #getPeriodosBySucursal(sucursalId) {
        const segment = this.#dataManager?.cartera?.[sucursalId] || {};
        return Object.keys(segment).sort((a, b) => `${b}`.localeCompare(`${a}`));
    }

    #showTable() {
        $('#mora-table-card').removeClass('d-none');
    }

    #hideTable() {
        $('#mora-table-card').addClass('d-none');
    }

    #handleError(error, message) {
        this.#logger.error(message, error);
        this.#errorHandler.handleError(error, message);
        $App.trigger('alert:error', {
            message: `${message}: ${error.message || 'Error desconocido'}`,
        });
    }

    #updateTitle() {
        const titleElement = $('#view_mora_title');
        let title;
        if (this.#periodo) {
            title = `Sucursal: ${this.#sucursalId} - Periodo: ${this.#formatPeriodo(this.#periodo)}`;
        } else {
            title = `Sucursal: ${this.#sucursalId}`;
        }
        titleElement.text(title);
    }

    #formatPeriodo(periodo) {
        if (!periodo || periodo.length !== 6) return periodo;
        return `${periodo.substring(4, 6)}/${periodo.substring(0, 4)}`;
    }

    destroy() {
        if (this.#filtrosView) this.#filtrosView.remove();
        if (this.#viewMora) this.#viewMora.remove();
        this.stopListening();
    }
}

export default MoraPresuntaApp;
