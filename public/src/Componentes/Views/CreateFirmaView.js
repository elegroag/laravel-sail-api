'use strict';

import { $App } from '@/App';

class CreateFirmaView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.template = $('#tmp_create_firma').html();
		this.firmaController;
		this.subHeader;
	}

	initialize() {
		this.firmaController = null;
	}

	get className() {
		return 'row page-container';
	}

	get events() {
		return {
			'click #undoBtn': 'undoEvent',
			'click #clearBtn': 'clearEvent',
			'click #processBtn': 'processEvent',
			'click #saveBtn': 'saveEvent',
		};
	}

	render() {
		let template = _.template(this.template);
		this.$el.html(template());
		this.__initSubHeader();
		return this;
	}

	afterRender() {
		this.firmaController = new CreateFirmaService();
		this.firmaController.init();
	}

	undoEvent(e) {
		e.preventDefault();
		this.firmaController.undoEvent();
	}

	clearEvent(e) {
		e.preventDefault();
		this.firmaController.clearEvent();
	}

	processEvent(e) {
		e.preventDefault();
		this.firmaController.processEvent();
	}

	saveEvent(e) {
		e.preventDefault();
		this.firmaController.saveEvent({
			model: this.model,
			callback: (response) => {
				if (response.success) {
					$App.trigger('alert:success', { message: response.msj });
					this.remove();
					if (this.model.get('id') === null) {
						$App.router.navigate('firma', { trigger: true });
					} else {
						$App.router.navigate('firma/' + this.model.get('id'), {
							trigger: true,
						});
					}
				} else {
					$App.trigger('alert:danger', { message: response.msj });
				}
			},
		});
	}

	__initSubHeader() {
		if (this.subHeader) this.subHeader.remove();
		this.subHeader = new SubHeaderFirmas({
			model: this.model,
			collection: [
				{
					id: 'bt_close_form',
					disabled: false,
					label: 'Salir',
					icon: 'ni ni-curved-next text-warning',
					active: false,
				},
				{
					id: 'bt_volver_solicitud',
					disabled: false,
					label: 'Volver a Solicitud',
					icon: 'ni ni-send text-success',
					active: false,
				},
				{
					id: 'bt_listar_firmas',
					disabled: false,
					label: 'Listar Firmas',
					icon: 'ni ni-ruler-pencil text-purple',
					active: false,
				},
				{
					id: 'bt_listar_firmas',
					disabled: true,
					label: 'Crear Firma',
					icon: 'fa fa-cog text-yellow',
					active: true,
				},
			],
		});

		this.listenTo(this.subHeader, 'route:firmas', this.__listarFirmas);
		this.listenTo(this.subHeader, 'route:request', this.__volverSolicitud);

		const subHeader = $App.layout.getRegion('subheader');
		subHeader.html(this.subHeader.render().$el);
	}

	__listarFirmas() {
		this.remove();
		if (this.model.get('id') === null) {
			$App.router.navigate('firma', { trigger: true });
		} else {
			$App.router.navigate('firma/' + this.model.get('id'), { trigger: true });
		}
	}

	__volverSolicitud() {
		this.remove();
		if (this.model.get('id') === null) {
			$App.router.navigate('create', { trigger: true });
		} else {
			$App.router.navigate('proceso/' + this.model.get('id'), { trigger: true });
		}
	}

	remove() {
		this.stopListening();
		if (this.subHeader) this.subHeader.remove();
		Backbone.View.prototype.remove.call(this);
	}
}

export { CreateFirmaView };
