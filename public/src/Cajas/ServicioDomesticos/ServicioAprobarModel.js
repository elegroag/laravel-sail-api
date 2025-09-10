'use strict';

class ServicioAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			estado: void 0,
		};
	}

	validate(attr = {}, options = void 0) {
		let _err = new Array();
		let erro;
		return _.isEmpty(_err) === true ? null : _err;
	}

	static Rules = {
		rules: {
			actapr: { required: true },
			nota: { required: true },
		},
		messages: {
			nombre: 'El campo es obligatorio.',
			email: 'Debe tener formato de email correcto.',
			telefono: 'El campo teléfono no contiene un formato correcto.',
			mensaje: 'El campo Mensaje es obligatorio',
			validator: 'Inerte los cuatro caracteres de la imagen superior.',
		},
	};
}

export { ServicioAprobarModel };
