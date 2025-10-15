import { RequestListView } from '@/Cajas/RequestListView';

export default class PensionadosView extends RequestListView {
	constructor(options = {}) {
		super({
			...options,
			titulo: 'Lista pensionados',
			titulo_detalle: 'Aprobar pensionado - ',
		});
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
		const template = _.template(document.getElementById('tmp_table').innerHTML);
		this.$el.html(template());
		this.__beforeRender();
		return this;
	}
}
