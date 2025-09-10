export default class DatosTrabajadorSisuView extends Backbone.View {
	constructor(options) {
		super(options);
	}

	get className() {
		return 'col';
	}

	initialize() {
		this.template = document.getElementById('tmp_trabajador').innerHTML;
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(template());
		return this;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
		};
	}

	loadTrabajador() {
		let _template = _.template($('#tmp_trabajador').html());
		$('#show_trabajador').html(_template(TRABAJADOR_SISU));
	}

	loadTrayectoria() {
		let _template = _.template($('#tmp_trayectoria').html());
		$('#show_trayectoria').html(
			_template({
				trayectorias: _TRAYECTORIAS,
			}),
		);
	}

	loadSalario() {
		let _template = _.template($('#tmp_salario').html());
		$('#show_salario').html(
			_template({
				salarios: _SALARIOS,
			}),
		);
	}

	remove() {
		console.log('OK remove');
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}
