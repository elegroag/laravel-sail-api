'use strict';

import { $App } from '@/App';

class FirmasView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.template = document.getElementById('tmp_firmas').innerHTML;
		this.subHeader;
		this.isNew;
	}

	get events() {
		return {
			"click [data-toggle='new-firma']": 'createFirma',
		};
	}

	createFirma(e) {
		e.preventDefault();
		this.remove();
		if (this.model.get('id') === null) {
			$App.router.navigate('fnew', { trigger: true });
		} else {
			$App.router.navigate('fnew/' + this.model.get('id'), { trigger: true });
		}
	}

	render() {
		const firma = this.collection[0];
		this.isNew = this.collection[1];

		let template = _.template(this.template);
		this.$el.html(template(firma));
		this.__initSubHeader();
		return this;
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
					disabled: true,
					label: 'Listar Firmas',
					icon: 'ni ni-ruler-pencil text-purple',
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
			$App.router.navigate('firma/', { trigger: true });
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

	get className() {
		return 'row page-container';
	}

	/**
	 * @override
	 */
	remove() {
		this.stopListening();
		if (this.subHeader) this.subHeader.remove();
		Backbone.View.prototype.remove.call(this);
	}
}

export { FirmasView };
