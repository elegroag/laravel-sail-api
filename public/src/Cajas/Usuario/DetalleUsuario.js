import { LayoutUsuario } from './views/LayoutUsuario';
import { DetalleUsuarioView } from './views/DetalleUsuarioView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';

export class DetalleUsuario {
	layout = null;

	constructor(options) {
		this.region = options.region;
		_.extend(this, Backbone.Events);
		window.App.Collections.formParams = null;
	}

	detalleUsuario(model) {
		this.layout = new LayoutUsuario();
		this.region.show(this.layout);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Usuarios Externos',
				detalle: 'Listado de usuarios externos',
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		window.App.trigger('syncro', {
			url: 'params',
			data: {},
			callback: (response) => {
				if (_.isNull(window.App.Collections.formParams)) window.App.Collections.formParams = [];
				_.extend(window.App.Collections.formParams, response.data);

				const view = new DetalleUsuarioView({
					model: model,
					collection: this.__serealizeParams(),
				});

				this.listenTo(view, 'form:save', this.__savePerfil);
				this.layout.getRegion('body').show(view);
			},
		});
	}

	__savePerfil(transfer) {
		const { entity, callback } = transfer;
		const url = 'guardar';
		window.App.trigger('syncro', {
			url: url,
			data: entity.toJSON(),
			callback: (response) => {
				return callback(response);
			},
		});
	}

	__serealizeParams() {
		const resources = _.keys(window.App.Collections.formParams);
		const collection = _.map(resources, (item) => {
			return {
				name: item,
				type: 'select',
				placeholder: item,
				search: item,
			};
		});
		return collection;
	}

	destroy() {
		this.region.remove();
		// @ts-ignore
		this.stopListening();
	}
}
