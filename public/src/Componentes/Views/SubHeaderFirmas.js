'use strict';

import { $App } from '@/App';

const { ModelView } = require('./ModelView');

class SubHeaderFirmas extends ModelView {
	constructor(options = {}) {
		super(options);
		this.isNew = options.isNew;

		const items = _.map(this.collection, (item, index) => {
			let disabled = item.disabled === true ? 'disabled' : '';
			let active = item.active === true ? 'active' : '';
			return `<li class="nav-item">
                <a class="nav-link ${disabled} ${active}" id="${item.id}">
                    <i class='${item.icon}'></i> ${item.label}
                </a>
            </li>`;
		});
		const html = items.join('');
		this.template = _.template(
			`<div class="col-auto"><ul class="nav nav-pills mb-1" role="tablist">${html}</ul></div>`,
		);
	}

	get className() {
		return 'row align-content-between';
	}

	get events() {
		return {
			'click #bt_close_form': 'closeForm',
			'click #bt_listar_firmas': 'listarFirmas',
			'click #bt_volver_solicitud': 'volverSolicitud',
		};
	}

	remove() {
		console.log('OK remove subheader');
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}

	closeForm(event) {
		event.preventDefault();
		$App.trigger('confirma', {
			message: '¡Esta seguro que desea salir y volver a la lista principal!',
			callback: (status) => {
				if (status) {
					$App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	listarFirmas(e) {
		e.preventDefault();
		this.trigger('route:firmas');
	}

	volverSolicitud(e) {
		e.preventDefault();
		this.trigger('route:request');
	}
}

export { SubHeaderFirmas };
