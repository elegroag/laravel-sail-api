'use strict';

import { $App } from '@/App';

class SubHeaderView extends Backbone.View {
	constructor(options) {
		super(options);
	}

	initialize(options = {}) {
		this.listenTo(options.model, 'change', this.render);
	}

	render() {
		const template = _.template(document.getElementById('tmp_subheader').innerHTML);
		this.$el.html(
			template({
				model: this.model.toJSON(),
				items: this.collection,
			}),
		);
		return this;
	}

	get className() {
		return 'row align-content-between';
	}

	get events() {
		return {
			'show.bs.tab a[data-bs-toggle="pill"]': 'actionSubHeader',
			'click #closeForm': 'closeForm',
		};
	}

	actionSubHeader(e) {
		const action = $(e.target).attr('aria-controls');
		switch (action) {
			case 'seguimiento':
				this.trigger('show:seguimiento');
				break;
			case 'documentos_adjuntos':
				this.trigger('show:documentos');
				break;
			case 'enviar_radicado':
				this.trigger('show:enviar');
				break;
			default:
				break;
		}
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
}

export { SubHeaderView };
