import { $App } from '@/App';
import ErrorHandler from '@/Common/ErrorHandler';
import Logger from '@/Common/Logger';
import { Region } from '@/Common/Region';
import MoraLayout from './views/MoraLayout';
import MoraPresuntaView from './views/MoraPresuntaView';

const ESTADO_LABEL = {
	A: 'Activa',
	I: 'Inactiva',
	S: 'Suspendida',
};

const ESTADO_CLASS = {
	A: 'bg-success',
	I: 'bg-secondary',
	S: 'bg-warning text-dark',
};

class MoraPresuntaApp {
	#dataManager;
	#errorHandler;
	#viewMora;
	#logger;
	#layout;

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

	execute() {
		this.#initialize();
	}

	#initialize() {
		try {
			const region = new Region({ el: '#boneLayout' });
			region.show(this.#layout);
			this.#renderBloques();
		} catch (error) {
			this.#handleError(error, 'Error al inicializar la aplicación');
		}
	}

	#formatPeriodo(periodo) {
		if (!periodo || `${periodo}`.length !== 6) return periodo;
		return `${`${periodo}`.substring(4, 6)}/${`${periodo}`.substring(0, 4)}`;
	}

	#formatMoney(value) {
		const n = Number(value) || 0;
		return n.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
	}

	#buildBloques() {
		const sucursales = this.#dataManager?.sucursales || [];
		const cartera = this.#dataManager?.cartera || {};

		return sucursales.map((s) => {
			const codsuc = `${s.codsuc}`;
			const filasRaw = cartera[codsuc] || [];
			const filas = filasRaw.map((r) => ({
				periodo: r.periodo,
				periodo_fmt: this.#formatPeriodo(r.periodo),
				valcar: Number(r.valcar) || 0,
				valcar_fmt: this.#formatMoney(r.valcar),
				tiene_valor: (Number(r.valcar) || 0) > 0,
			}));
			const total = filas.reduce((acc, f) => acc + f.valcar, 0);

			return {
				codsuc,
				detalle: s.detalle || '',
				estado: s.estado || '',
				estado_label: ESTADO_LABEL[s.estado] || s.estado || '',
				estado_class: ESTADO_CLASS[s.estado] || 'bg-secondary',
				tiene_cartera: filas.length > 0,
				filas,
				total,
				total_fmt: this.#formatMoney(total),
			};
		});
	}

	#renderBloques() {
		if (this.#viewMora) this.#viewMora.remove();
		this.#viewMora = new MoraPresuntaView({
			model: {
				bloques: this.#buildBloques(),
			},
			errorHandler: this.#errorHandler,
		});
		this.#layout.getRegion('bloques').show(this.#viewMora);
	}

	#handleError(error, message) {
		this.#logger.error(message, error);
		this.#errorHandler.handleError(error, message);
		$App.trigger('alert:error', {
			message: `${message}: ${error.message || 'Error desconocido'}`,
		});
	}

	destroy() {
		if (this.#viewMora) this.#viewMora.remove();
		this.stopListening();
	}
}

export default MoraPresuntaApp;
