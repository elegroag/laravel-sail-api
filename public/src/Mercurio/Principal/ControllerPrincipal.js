import { ServiciosView } from './ServiciosView';
import { PrincipalLayout } from './PrincipalLayout';

class ControllerPrincipal {
	constructor(options) {
		this.App = null;
		this.region = null;
		this.layout = null;

		_.extend(this, options);
		_.extend(this, Backbone.Events);
		this.once('syncro:request', this.__syncroSolicitudes);

		this.layout = new PrincipalLayout();
		this.listenTo(this.layout, 'form:cancel', this.destroy);
		this.region.show(this.layout);
	}

	listServices() {
		this.__validaSyncro();
		this.__buscarServicios({
			callback: (response) => {
				if (response) {
					if (this.App.Collections.afiliacion) {
						_.each(this.App.Collections.afiliacion, (item) => {
							item.tipo = 'afiliacion';
							const view = new ServiciosView({ model: item });
							this.layout.getRegion('afiliaciones').append(view);
						});
					}

					if (this.App.Collections.productos) {
						$("[for='productos']").fadeIn();

						_.each(this.App.Collections.productos, (item) => {
							item.tipo = 'productos';
							const view = new ServiciosView({ model: item });
							this.layout.getRegion('productos').append(view);
						});
					}

					if (this.App.Collections.consultas) {
						$("[for='consultas']").fadeIn();
						_.each(this.App.Collections.consultas, (item) => {
							item.tipo = 'consultas';
							const view = new ServiciosView({ model: item });
							this.layout.getRegion('consultas').append(view);
						});
					}
				}
			},
			silent: false,
		});
	}

	descargaDocumentos() {}

	__buscarServicios(transfer) {
		const { callback, silent } = transfer;
		this.App.trigger('syncro', {
			url: this.App.url('servicios'),
			data: {},
			silent,
			callback: (response) => {
				if (response) {
					if (response.success === true) {
						_.extend(this.App.Collections, response.data);
						return callback(response.msj);
					}
				}
				return callback(false);
			},
		});
	}

	__syncroSolicitudes() {
		this.App.trigger('syncro', {
			url: this.App.url('actualizaEstadoSolicitudes'),
			data: {},
			callback: (response) => {
				if (response.success) {
					this.App.trigger('alert:success', { message: response.msj });
				}
			},
		});
	}

	__validaSyncro() {
		this.App.trigger('syncro', {
			url: this.App.url('valida_syncro'),
			data: {},
			callback: (response) => {
				if (response.success) {
					if (response.data.syncron) {
						this.trigger('syncro:request');
					}
					$('#show_date_syncron').html(response.data.ultimo_syncron);
				}
			},
			silent: true,
		});
	}

	destroy() {
		this.stopListening();
		if (this.region && this.region instanceof Region) this.region.remove();
	}
}

export { ControllerPrincipal };
