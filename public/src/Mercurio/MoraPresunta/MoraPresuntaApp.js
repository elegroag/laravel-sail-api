import { $App } from '@/App';
import { Region } from '@/Common/Region';
import MoraPresuntaView from './views/MoraPresuntaView';
import ErrorHandler from '@/Common/ErrorHandler';
import Logger from '@/Common/Logger';
import MoraLayout from './views/MoraLayout';
import { CollectionView } from '@/Common/CollectionView';
import { ModelView } from '@/Common/ModelView';

class SucursalButton extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(
			'<button data-codsuc="<%= codsuc %>" class="btn btn-primary btn-sucursal <%= (isActive) ? "active" : "" %>"><%= codsuc %></button>',
		);
	}
}

class SucursalesView extends CollectionView {
	constructor(options = {}) {
		super(options);
		this.modelView = SucursalButton;
	}

	get events() {
		return {
			'click .btn-sucursal': 'changeSucursal',
		};
	}

	changeSucursal(e) {
		e.preventDefault();
		const sucursalId = e.currentTarget.dataset.codsuc;
		if (!sucursalId) return;
		this.trigger('change:sucursal', sucursalId);
	}
}

class Sucursal extends Backbone.Model {}

class Sucursales extends Backbone.Collection {
	constructor(options = {}) {
		super(options);
	}

	get model() {
		return Sucursal;
	}
}

class PeriodosView extends Backbone.View {
	constructor(options = {}) {
		super({ ...options, className: 'list-group' });
	}

	render() {
		let html = '';
		for (const key in this.collection) {
			const element = this.collection[key];
			html += `<a data-periodo="${element}" class='list-group-item list-group-item-action'>${element}</a>`;
		}
		this.$el.append(html);
		this.$(`.list-group-item[data-periodo="${this.collection.at(0)}"]`).addClass(
			'active',
		);
		return this;
	}

	get events() {
		return {
			'click .list-group-item': 'changePeriodo',
		};
	}

	changePeriodo(e) {
		e.preventDefault();
		const periodo = e.currentTarget.dataset.periodo;
		if (!periodo) return;
		this.$(e.currentTarget).addClass('active').siblings().removeClass('active');
		this.trigger('change:periodo', periodo);
	}
}

class MoraPresuntaApp {
	#dataManager;
	#errorHandler;
	#viewMora;
	#logger;
	#layout;
	#sucursalId;
	#collectionPeriodos;
	#periodo;
	#collectionSucursales;

	constructor() {
		_.extend(this, Backbone.Events);
		this.#errorHandler = new ErrorHandler();
		this.#logger = new Logger();
		this.#layout = new MoraLayout();
	}

	setDataManager(dataManager) {
		this.#dataManager = dataManager;
	}

	execute(sucursalId = null, periodo = null) {
		this.#initialize();
		if (sucursalId) this.#sucursalId = sucursalId;
		if (periodo) this.#periodo = periodo;
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

		this.#renderSucursales();
		this.#renderPeriodos();
		this.#updateView();
		this.#updateTitle();
	}

	#updateView() {
		if (this.#viewMora) this.#viewMora.remove();
		this.#viewMora = new MoraPresuntaView({
			model: {
				codsuc: this.#sucursalId,
				cartera: this.#dataManager.cartera[this.#sucursalId][this.#periodo],
				periodos: this.#dataManager.periodos,
				sucursales: this.#dataManager.sucursales,
			},
			errorHandler: this.#errorHandler,
		});
		this.#layout.getRegion('table').show(this.#viewMora);
	}

	#handleError(error, message) {
		this.#logger.error(message, error);
		this.#errorHandler.handleError(error, message);
		$App.trigger('alert:error', {
			message: `${message}: ${error.message || 'Error desconocido'}`,
		});
	}

	#renderSucursales() {
		let dataSucursales;
		if (this.#sucursalId) {
			dataSucursales = _.map(this.#dataManager.sucursales, (sucursal) => {
				sucursal.isActive = sucursal.codsuc == this.#sucursalId ? true : false;
				return sucursal;
			});
		} else {
			dataSucursales = _.map(this.#dataManager.sucursales, (sucursal) => {
				sucursal.isActive = false;
				return sucursal;
			});
			this.#sucursalId = dataSucursales[0].codsuc;
		}
		const collection = new Sucursales(dataSucursales);
		const container = this.#layout.getRegion('sucursales');
		this.#collectionSucursales = new SucursalesView({
			collection: collection,
		});

		this.listenTo(this.#collectionSucursales, 'change:sucursal', (sucursalId) => {
			if (this.#sucursalId == sucursalId) return;
			this.destroy();
			setTimeout(() => {
				$App.router.navigate(`list/${sucursalId}`, { trigger: true });
			}, 50);
		});

		container.show(this.#collectionSucursales);
	}

	#renderPeriodos() {
		const container = this.#layout.getRegion('periodos');
		const segmentPeriods = _.filter(this.#dataManager.cartera, (periodo, codsuc) => {
			return codsuc == this.#sucursalId ? periodo : null;
		});

		const dataPeriodos = _.map(segmentPeriods, (periodo) => {
			return Object.keys(periodo);
		});

		if (!this.#periodo) this.#periodo = dataPeriodos[0][0];
		this.#collectionPeriodos = new PeriodosView({
			collection: dataPeriodos[0],
		});

		this.listenTo(this.#collectionPeriodos, 'change:periodo', (periodo) => {
			if (this.#periodo == periodo) return;
			this.#periodo = periodo;
			setTimeout(() => {
				this.#updateView();
				this.#updateTitle();
			}, 50);
		});

		container.show(this.#collectionPeriodos);
	}

	#updateTitle() {
		const titleElement = $('#view_mora_title');
		let title;
		if (this.#periodo) {
			title = `Sucursal: ${this.#sucursalId} - Periodo: ${this.#formatPeriodo(
				this.#periodo,
			)}`;
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
		this.#collectionPeriodos.remove();
		this.#collectionSucursales.remove();
		this.#viewMora.remove();
		this.stopListening();
	}
}

export default MoraPresuntaApp;
