import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class IndependienteModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			cedtra: void 0,
			tipdoc: void 0,
			priape: '',
			prinom: '',
			fecnac: '',
			ciunac: '',
			sexo: '',
			estciv: '',
			cabhog: '',
			direccion: '',
			dirlab: '',
			telefono: '',
			celular: '',
			email: void 0,
			fecing: void 0,
			salario: 0,
			captra: '',
			tipdis: '',
			nivedu: '',
			rural: '',
			horas: 0,
			tipcon: '',
			vivienda: '',
			tipafi: '',
			cargo: '',
			autoriza: '',
			trasin: '',
			tipjor: '',
			comision: '',
			ruralt: '',
			orisex: '',
			facvul: '',
			peretn: '',
			codsuc: void 0,
			tippag: void 0,
			otra_empresa: '',
			estado: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(IndependienteModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			nit: { required: true, minlength: 6 },
			razsoc: { required: true, minlength: 6 },
			sigla: { required: false },
			digver: { required: true, minlength: 1 },
			calemp: { required: true, minlength: 1 },
			cedrep: { required: true, minlength: 6 },
			repleg: { required: true, minlength: 10 },
			telefono: { required: true, minlength: 7 },
			celular: { required: true, minlength: 10 },
			fax: { required: false },
			fecini: { required: true, minlength: 10 },
			tottra: { required: true },
			valnom: { required: true, minlength: 6 },
			dirpri: { required: false },
			ciupri: { required: false },
			celpri: { required: false },
			tipemp: { required: true },
			tipper: { required: true },
		},
		messages: {
			nombre: { required: 'El campo es obligatorio.' },
			email: { required: 'Debe tener formato de email correcto.' },
			telefono: { required: 'El campo teléfono no contiene un formato correcto.' },
			mensaje: { required: 'El campo Mensaje es obligatorio' },
			validator: { required: 'Inerte los cuatro caracteres de la imagen superior.' },
			nit: { required: 'El campo nit es obligatorio.' },
			digver: { required: 'El campo digver es obligatorio.' },
			calemp: { required: 'El campo calemp es obligatorio.' },
			cedrep: { required: 'El campo cedrep es obligatorio.' },
			repleg: { required: 'El campo repleg es obligatorio.' },
			celular: { required: 'El campo celular es obligatorio.' },
			fax: { required: 'El campo fax es obligatorio.' },
			fecini: { required: 'El campo fecini es obligatorio.' },
			tottra: { required: 'El campo tottra es obligatorio.' },
			valnom: { required: 'El campo valnom es obligatorio.' },
			dirpri: { required: 'El campo dirpri es obligatorio.' },
			ciupri: { required: 'El campo ciupri es obligatorio.' },
			celpri: { required: 'El campo celpri es obligatorio.' },
			tipemp: { required: 'El campo tipemp es obligatorio.' },
			tipper: { required: 'El campo tipper es obligatorio.' },
		},
	};
}
