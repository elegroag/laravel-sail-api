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

		this.App = options.App || window.App;
		this.currentApp = this.App.startSubApplication(MoraPresuntaApp);
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
				if (response.success) {
					this.#dataManager = response.data;
				} else {
					this.#handleError(response.msj, 'Error al cargar los datos');
				}
			}
			this.currentApp.setDataManager(this.#dataManager);
		} catch (error) {
			this.#handleError(error, 'Error al cargar los datos');
			throw error;
		}
	}

	#handleError(error, message) {
		this.App.trigger('alert:error', { message: `${message}: ${error.message}` });
	}

	#fetchData() {
		return new Promise((resolve, reject) => {
			this.App.trigger('syncro', {
				url: this.App.url('subsidioemp/mora_presunta'),
				data: {},
				callback: (response) => resolve(response),
				error: (error) => reject(error.message),
			});
		});
	}
}

export { RouterMoraPresunta };
