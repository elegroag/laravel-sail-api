import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import DatosTrabajadorView from './views/DatosTrabajadorView';
import DatosTrabajadorModel from './models/DatosTrabajadorModel';
import FormDatosTrabajador from './views/FormDatosTrabajador';

class ControllerDatosTrabajador extends ControllerRequest {
	solicitudModel = null;

	constructor(options = {}) {
		super({
			...options,
			EntityModel: DatosTrabajadorModel,
			TableView: DatosTrabajadorView,
			FormRequest: FormDatosTrabajador,
			tipo: 'T',
			headerOptions: {
				estado: '',
				tipo: 'T',
				url_nueva: window.App.url(window.ServerController + '/nueva'),
				breadcrumb_menu: 'Crear solicitud',
				titulo: 'Actualiza datos trabajador',
				url_masivo: null,
				isNew: null,
				create: 'enabled',
			},
		});

		if (this.services) _.extend(this, this.services);
		this.on('form:cancel', this.destroy);
		this.on('form:digit', this.afiService.digitVer);
		this.on('params', this.afiService.paramsServer);
		this.App.Collections.formParams = null;
	}

	createRequest() {
		this.solicitudModel = new DatosTrabajadorModel();
		this.trigger('params', {
			silent: true,
			callback: (response) => {
				if (response) {
					this.App.trigger('syncro', {
						url: this.App.url(window.ServerController+ '/infor'),
						silent: false,
						callback: (response) => {
							if (response) {
								if (response.success) {
									this.renderCreateRequest(response.data);
								} else {
									this.App.trigger('alert:error', { message: response.msj });
								}
							}
						},
					});
				}
			},
		});
	}

	renderCreateRequest(collection = {}) {
		const view = new FormDatosTrabajador({
			model: this.solicitudModel,
			collection: {
				paramsForm: this.serealizeParams(),
				dataDefault: collection,
			},
			isNew: this.solicitudModel.get('id') == null,
			region: this.region,
		});

		this.listenTo(view, 'form:save', this.afiService.saveFormData);
		this.listenTo(view, 'form:send', this.afiService.sendRadicado);
		this.listenTo(view, 'form:find', this.afiService.validePk);

		this.App.layout.getRegion('body').show(view);

		this.afiService.initHeaderView({
			...this.headerOptions,
			isNew: this.solicitudModel.get('id') == null,
			breadcrumb_menu: 'Crear solicitud',
			create: 'disabled',
		});
	}

	procesoRute(id) {
		this.solicitudModel = new DatosTrabajadorModel();
		if (_.isNull(this.App.Collections.formParams)) {
			this.trigger('params', {
				silent: true,
				callback: (response) => {
					if (response) {
						this.afiService.serachRequestServer({
							id: id,
							callback: (response) => {
								if (response) {
									if (response.success) {
										this.solicitudModel.set(response.data);
										this.renderCreateRequest({});
									} else {
										this.App.trigger('alert:error', { message: response.msj });
									}
								}
							},
						});
					}
				},
			});
		} else {
			this.afiService.serachRequestServer({
				id: id,
				callback: (response) => {
					if (response) {
						if (response.success) {
							this.solicitudModel.set(response.data);
							this.renderCreateRequest({});
						} else {
							this.App.trigger('alert:error', { message: response.msj });
						}
					}
				},
			});
		}
	}
}

export { ControllerDatosTrabajador };
