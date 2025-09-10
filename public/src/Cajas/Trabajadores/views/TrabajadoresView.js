import { RequestListView } from '@/Cajas/RequestListView';

export default class TrabajadoresView extends RequestListView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_table').innerHTML);
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
			"click [toggle-event='buscar']": 'buscarPagina',
			"change [toggle-event='change']": 'changeCantidad',
			'click #btPendienteEmail': 'irPendienteEmail',
			'click #btenviar': 'sendMail',
		};
	}

	render() {
		this.$el.html(this.template());
		this.__beforeRender();
		return this;
	}

	sendMail() {
		let nerr = 0;
		let _cedtra = this.$('#cedtra').val();
		if (_cedtra == '') {
			nerr++;
			document.querySelector('.error_cedtra').innerHTML =
				'<span>El campo cedula es un valor requerido.</span>';
		} else {
			let express = /^([0-9]){8,13}$/;
			if (!express.test(_cedtra.toString())) {
				nerr++;
				document.querySelector('.error_cedtra').innerHTML =
					'<span>La cedula no es un valor valido para continuar.</span>';
			}
		}
		return nerr == 0 ? $('#form_pendiente').submit() : false;
	}
}
