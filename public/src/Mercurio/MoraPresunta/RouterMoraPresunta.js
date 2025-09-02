import { $App } from '@/App';
import MoraPresuntaApp from './MoraPresuntaApp';

class RouterMoraPresunta extends Backbone.Router {
	#dataManager;

	constructor(options = {}) {
		super({
			...options,
			routes: {
				list: 'renderDefault',
				'list/:sucursal': 'renderBySucursal',
				'list/:sucursal/:periodo': 'renderBySucursalPeriodo',
			},
		});

		this.currentApp = $App.startSubApplication(MoraPresuntaApp);
		this._bindRoutes();
	}

	async renderBySucursalPeriodo(sucursalId, periodo) {
		await this.#loadData();
		this.currentApp.execute(sucursalId, periodo);
	}

	async renderBySucursal(sucursalId) {
		await this.#loadData();
		this.currentApp.execute(sucursalId);
	}

	async renderDefault() {
		await this.#loadData();
		this.currentApp.execute();
	}

	async #loadData() {
		try {
			if (!this.#dataManager) {
				const response = await this.#fetchData();
				this.#dataManager = response.data;
			}
			this.currentApp.setDataManager(this.#dataManager);
		} catch (error) {
			this.#handleError(error, 'Error al cargar los datos');
			throw error;
		}
	}

	#handleError(error, message) {
		$App.trigger('alert:error', { message: `${message}: ${error.message}` });
	}

	#fetchData() {
		return new Promise((resolve, reject) => {
			$App.trigger('syncro', {
				url: $App.url('mora_presunta'),
				data: {},
				callback: (response) => resolve(response),
				error: (error) => reject(error.message),
			});
		});
	}
}

export { RouterMoraPresunta };
