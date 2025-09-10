import { ModelView } from '@/Common/ModelView';

export default class EmpresaSisuView extends ModelView {
	constructor(options) {
		super({
			...options,
			className: 'col',
			onRender: () => this.afterRender(),
		});
		this.template = _.template(document.getElementById('tmp_empresa').innerHTML);
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
		};
	}

	afterRender() {
		this.__loadEmpresa();
		this.__loadTrayectoria();
		this.__loadSucursales();
		this.__loadListas();
	}

	__loadEmpresa = function () {
		let _template = _.template($('#tmp_empresa').html());
		this.$el.find('#show_empresa').html(_template(this.collection.empresa));
	};

	__loadTrayectoria = function () {
		let _template = _.template($('#tmp_trayectoria').html());
		this.$el
			.find('#show_trayectoria')
			.html(_template({ trayectoria: this.collection.trayectoria }));
	};

	__loadSucursales = function () {
		let _template = _.template($('#tmp_sucursales').html());
		this.$el
			.find('#show_sucursales')
			.html(_template({ sucursales: this.collection.sucursales }));
	};

	__loadListas = function () {
		let _template = _.template($('#tmp_listas').html());
		this.$el.find('#show_listas').html(_template({ listas: this.collection.listas }));
	};
}
