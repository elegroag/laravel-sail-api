export default class EmpresaModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			nit: void 0,
			razsoc: '',
			sigla: '',
			digver: void 0,
			calemp: '',
			cedrep: '',
			repleg: '',
			telefono: void 0,
			celular: void 0,
			email: '',
			fecini: void 0,
			tottra: void 0,
			valnom: void 0,
			tipemp: '',
			tipper: '',
			priape: '',
			prinom: '',
			segnom: '',
			segape: '',
			tipdoc: void 0,
			codact: void 0,
			coddocrepleg: void 0,
			estado: void 0,
			tranoms: [],
		};
	}

	validate(attr = {}, options = void 0) {
		let _err = new Array();
		return _.isEmpty(_err) === true ? null : _err;
	}

	static Rules = {
		rules: {
			nit: { required: true, minlength: 6 },
			razsoc: { required: true, minlength: 5 },
			sigla: { required: false },
			digver: { required: true, minlength: 1 },
			calemp: { required: true, minlength: 1 },
			cedrep: { required: true, minlength: 6 },
			repleg: { required: true, minlength: 6 },
			telefono: { required: true, minlength: 7 },
			celular: { required: true, minlength: 10 },
			email: { required: true },
			fecini: { required: true, minlength: 10 },
			tottra: { required: true },
			valnom: { required: true, minlength: 6 },
			dirpri: { required: false },
			ciupri: { required: true },
			celpri: { required: false },
			emailpri: { required: false },
			tipemp: { required: true },
			tipper: { required: true },
			prinom: { required: true },
			priape: { required: true },
			fecapr: { required: true },
		},
		messages: {
			nombre: 'El campo es obligatorio.',
			email: 'Debe tener formato de email correcto.',
			telefono: 'El campo teléfono no contiene un formato correcto.',
			mensaje: 'El campo Mensaje es obligatorio',
			validator: 'Inerte los cuatro caracteres de la imagen superior.',
			nit: 'El campo nit es obligatorio.',
			razsoc: 'El campo razón social es obligatorio.',
			sigla: 'El campo sigla es obligatorio.',
			digver: 'El campo digito verificador es obligatorio.',
			calemp: 'El campo calidad empresa es obligatorio.',
			cedrep: 'El campo cédula representante es obligatorio.',
			repleg: 'El campo nombre representante es obligatorio.',
			celular: 'El campo celular es obligatorio.',
			fecini: 'El campo fecha inicio es obligatorio.',
			tottra: 'El campo total trabajadores es obligatorio.',
			valnom: 'El campo valor nomina es obligatorio.',
			dirpri: 'El campo dirección principal es obligatorio.',
			ciupri: 'El campo ciudad principal es obligatorio.',
			celpri: 'El campo celular principal es obligatorio.',
			emailpri: 'El campo email principal es obligatorio.',
			tipemp: 'El campo tipo empresa es obligatorio.',
			tipper: 'El campo tipo persona es obligatorio.',
			prinom: 'El campo nombres es obligatorio.',
			priape: 'El campo apellidos es obligatorio.',
			fecapr: 'El campo fecha aprobación resolución es obligatorio.',
		},
	};
}
