import { is_numeric, Testeo } from '@/Core';

class UsuarioModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'documento';
	}

	get defaults() {
		return {
			documento: null,
			tipo: null,
			coddoc: null,
			clave: '',
			nombre: '',
			email: '',
			feccla: '',
			autoriza: '',
			codciu: '',
			fecreg: '',
			estado: '',
			fecha_syncron: '',
			coddoc_detalle: '',
			tipo_detalle: '',
			codciu_detalle: '',
			estado_detalle: '',
			isEdit: -1,
			newclave: null,
			old_coddoc: null,
		};
	}

	validate(attr = {}, options = void 0) {
		let _err = [];
		let erro = undefined;

		if (
			(erro = Testeo.vacio({
				attr: attr.coddoc,
				target: 'coddoc',
				label: 'tipo documento',
			}))
		) {
			_err.push(erro);
		} else {
			if (attr.coddoc == '@') {
				_err.push('El tipo de documento es requerido.');
			}
		}

		if ((erro = Testeo.email({ attr: attr.email, target: 'email', label: 'email' }))) {
			_err.push(erro);
		}

		if (
			(erro = Testeo.vacio({
				attr: attr.documento,
				target: 'documento',
				label: 'documento',
			}))
		) {
			_err.push(erro);
		} else {
			if (!is_numeric(attr.documento)) {
				_err.push('El documento no es un valor valido.');
			}
		}

		if ((erro = Testeo.vacio({ attr: attr.codciu, target: 'codciu', label: 'ciudad' }))) {
			_err.push(erro);
		} else {
			if (attr.codciu == '0000') {
				_err.push('La ciudad de notificación es requerida.');
			}
		}

		if ((erro = Testeo.vacio({ attr: attr.tipo, target: 'tipo', label: 'tipo' }))) {
			_err.push(erro);
		}

		if ((erro = Testeo.vacio({ attr: attr.clave, target: 'clave', label: 'clave' }))) {
			_err.push(erro);
		}

		if ((erro = Testeo.vacio({ attr: attr.nombre, target: 'nombre', label: 'nombre' }))) {
			_err.push(erro);
		}

		return _.isEmpty(_err) === true ? null : _err;
	}

	static Rules = {
		clave: { required: true },
		documento: { required: true, minlength: 6 },
		coddoc: { required: true },
		tipo: { required: true },
		codciu: { required: true },
		email: { required: true },
		nombre: { required: true },
		newclave: { required: true, minlength: 5 },
	};
}

export { UsuarioModel };
