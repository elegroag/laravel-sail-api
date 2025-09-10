import { ModelView } from '@/Common/ModelView';

class NotificarView extends ModelView {
	constructor(parameters) {
		super(parameters);
		this.template = _.template(document.getElementById('tmp_notificar').innerHTML);
	}

	get events() {
		return {
			'click #btnNotificar': 'notificarAction',
		};
	}

	notificarAction() {
		let nerr = 0;
		const _cedtra = this.$el.find('#cedtra').val();
		if (_cedtra == '') {
			nerr++;
			document.querySelector('.error_cedtra').innerHTML =
				'<span>El campo cedula es un valor requerido.</span>';
		} else {
			let express = /^([0-9]){8,13}$/;
			if (!express.test(_cedtra)) {
				nerr++;
				document.querySelector('.error_cedtra').innerHTML =
					'<span>La cedula no es un valor valido para continuar.</span>';
			}
		}
		if (nerr == 0) this.$el.find('#form_pendiente').submit();
	}
}

export { NotificarView };
