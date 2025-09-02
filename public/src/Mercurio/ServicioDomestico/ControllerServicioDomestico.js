import { AfiliationService } from '@/Componentes/Services/AfiliationService';
import { $App } from '@/App';
import { PensionadosView } from './views/ServicioDomesticosView';
import { PensionadoModel } from '../Pensionados/models/PensionadoModel';
import { FormPensionadoView } from '../Pensionados/views/FormPensionadoView';
import { ControllerRequest } from '@/Mercurio/ControllerRequest';

class ControllerServicioDomestico extends ControllerRequest {
	constructor(options) {
		super(options);
		_.extend(this, this.services);
		if (this.afiService == null) this.afiService = new AfiliationService();

		this.on('form:cancel', this.destroy);

		this.on('form:find', this.afiService.validePk);

		this.once('params', this.afiService.paramsServer);
	}

	listRequests(tipo) {
		this.initialize();
		this.trigger('params', () => false, true);

		const view = new PensionadosView({ model: { tipo } });
		this.listenTo(view, 'load:table', this.afiService.findDataTable);
		this.listenTo(view, 'remove:solicitud', this.afiService.cancelaSolicitud);

		$App.layout.getRegion('subheader').fadeOut('slow');
		$App.layout.getRegion('body').show(view);

		const options = {
			estado: tipo,
			tipo: 'E',
			url_nueva: $App.url('nueva'),
			breadcrumb_menu: 'Listar solicitudes',
			titulo: 'Afiliación pensionados',
			url_masivo: null,
			isNew: false,
		};
		this.afiService.initHeaderView(options);
	}

	createRequest(id) {
		this.initialize();
		if (id) {
			if (_.size($App.Collections) == 0) {
				this.trigger(
					'params',
					() => {
						this.afiService.serachRequestServer({
							id: id,
							callback: (response) => {
								if (response) {
									if (response.success) {
										const model = new PensionadoModel(response.data);
										this.renderCreateRequest(model);
									} else {
										$App.trigger('alert:error', { message: response.msj });
									}
								}
							},
						});
					},
					true,
				);
			} else {
				this.afiService.serachRequestServer({
					id: id,
					callback: (response) => {
						if (response) {
							if (response.success) {
								const model = new PensionadoModel(response.data);
								this.renderCreateRequest(model);
							} else {
								$App.trigger('alert:error', { message: response.msj });
							}
						}
					},
				});
			}
		} else {
			const model = new PensionadoModel();
			if (_.size($App.Collections) == 0) {
				this.trigger('params', (response) => {
					if (response) this.renderCreateRequest(model);
				});
			} else {
				this.renderCreateRequest(model);
			}
		}
	}

	renderCreateRequest(model) {
		const resources = _.keys($App.Collections);
		const collection = _.map(resources, (item) => {
			return {
				name: item,
				type: 'select',
				placeholder: item,
				search: item,
			};
		});

		const view = new FormPensionadoView({
			model,
			collection,
			isNew: model.get('id') == null,
		});

		this.listenTo(view, 'form:save', this.afiService.saveFormData);
		this.listenTo(view, 'form:send', this.afiService.sendRadicado);

		$App.layout.getRegion('body').show(view);

		const options = {
			estado: '',
			tipo: 'E',
			url_nueva: $App.url('nueva'),
			breadcrumb_menu: 'Crear solicitud',
			titulo: 'Afiliación pensionado',
			url_masivo: null,
			isNew: model.get('id') == null,
		};

		this.afiService.initHeaderView(options);

		$("[data-toggle='create']").attr('disabled', 'true');
	}
}

export { ControllerServicioDomestico };
