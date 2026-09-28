import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { EditarSolicitudView } from '@/Cajas/EditarSolicitudView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';

class SolicitudEditar extends ControllerValidation {
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	editarRequest(id, titulo = '') {
		this.App.trigger('syncro', {
			url: 'editar-formulario',
			data: { id },
			callback: (response) => {
				if (!response || !response.success) {
					if (response) {
						this.App.trigger('alert:warning', { message: response.msj });
					}
					this.App.router.navigate('info/' + id, { trigger: true, replace: true });
					return;
				}

				this.initialize();
				this.headerMain = new HeaderCajasView({
					model: {
						titulo: titulo,
						detalle: 'Editar información registrada',
						info: false,
					},
				});
				this.layout.getRegion('header').show(this.headerMain);

				this.headerView = new HeaderInfoView({
					model: {
						id: id,
						estado: response.data.estado,
						option: {
							volver: true,
							deshacer: false,
							aportes: false,
							editar: false,
							info: true,
							notificar: false,
							trayectoria: false,
						},
					},
				});
				this.listenTo(this.headerView, 'load:volver', this.__volverLista);
				this.listenTo(this.headerView, 'load:info', this.__infoRequest);
				this.layout.getRegion('subheader').show(this.headerView);

				const view = new EditarSolicitudView({
					model: new Backbone.Model(response.data),
					collection: { campos: response.campos },
				});
				this.listenTo(view, 'load:guardar', this.guardarSolicitud);
				this.layout.getRegion('body').show(view);
			},
		});
	}

	guardarSolicitud(transfer) {
		const { data, callback } = transfer;
		this.App.trigger('syncro', {
			url: 'editar-solicitud',
			data: data,
			callback: (response) => {
				callback(response);
				if (!response) {
					return;
				}
				if (response.success) {
					this.App.trigger('alert:success', { message: response.msj });
					this.__infoRequest({ id: data.id });
				} else {
					this.App.trigger('alert:warning', { message: response.msj });
				}
			},
		});
	}
}

export { SolicitudEditar };
