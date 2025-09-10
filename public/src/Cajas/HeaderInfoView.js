import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

class HeaderInfoView extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_info_header').innerHTML);
	}

	get className() {
		return 'col-auto';
	}

	get events() {
		return {
			"click [toggle-event='deshacer']": 'deshacerRequest',
			"click [toggle-event='aportes']": 'aportesEmpresa',
			"click [toggle-event='volver']": 'volverLista',
			"click [toggle-event='editar']": 'editarRequest',
			"click [toggle-event='notificar']": 'notificarRequest',
			"click [toggle-event='info']": 'infoRequest',
			"click [toggle-event='reaprobar']": 'reaprobarRequest',
			"click [toggle-event='trayectoria']": 'trayectoriaRequest',
		};
	}

	aportesEmpresa(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		$App.trigger('confirma', {
			message: `Se requiere de confirmar que desea buscar los aportes PILA y las nominas.`,
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('load:aportes', { id });
				}
			},
		});
	}

	deshacerRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		$App.trigger('confirma', {
			message: `Se requiere de confirmar que desea deshacer la aprobación de la afiliación y dejar en estado pendiente.`,
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('load:deshacer', {
						id: id,
						callback: (response) => {
							if (response && response.success) {
								this.trigger('load:volver', {});
							}
						},
					});
				}
			},
		});
	}

	reaprobarRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		$App.trigger('confirma', {
			message: `Se requiere de confirmar que desea reaprobar la afiliación.`,
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('load:reaprobar', {
						id: id,
						callback: (response) => {
							if (response && response.success) {
								this.trigger('load:volver', {});
							}
						},
					});
				}
			},
		});
	}

	volverLista(e) {
		e.preventDefault();
		this.trigger('load:volver', {});
	}

	editarRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		$App.trigger('confirma', {
			message: `Se requiere de confirmar que desea editar la afiliación de la empresa.`,
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('load:editar', { id });
				}
			},
		});
	}

	notificarRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		$App.trigger('confirma', {
			message: `Se requiere de confirmar que desea notificar la afiliación de la empresa.`,
			title: '¿Confirmar?',
			icon: 'warning',
			callback: (status) => {
				if (status) {
					this.trigger('load:notificar', { id });
				}
			},
		});
	}

	infoRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		this.trigger('load:info', { id });
	}

	trayectoriaRequest(e) {
		e.preventDefault();
		const id = this.$el.find(e.currentTarget).attr('data-cid');
		this.trigger('load:trayectoria', { id });
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { HeaderInfoView };
