import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class TrabajadorModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			nit: 0,
			razsoc: '',
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
			otra_empresa: '',
			estado: void 0,
			tippag: 'T',
			codban: 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(TrabajadorModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			cedtra: { required: true },
			tipdoc: { required: true },
			priape: { required: true },
			prinom: { required: true },
			fecnac: { required: true },
			ciunac: { required: true },
			sexo: { required: true },
			estciv: { required: true },
			cabhog: { required: true },
			direccion: { required: true },
			dirlab: { required: true },
			telefono: { required: true },
			celular: { required: true },
			email: { required: true },
			fecing: { required: true },
			salario: { required: true },
			captra: { required: true },
			tipdis: { required: true },
			nivedu: { required: true },
			rural: { required: true },
			horas: { required: true },
			tipcon: { required: true },
			vivienda: { required: true },
			tipafi: { required: true },
			cargo: { required: true },
			autoriza: { required: true },
			trasin: { required: true },
			tipjor: { required: true },
			comision: { required: true },
			ruralt: { required: true },
			orisex: { required: true },
			facvul: { required: true },
			peretn: { required: true },
			codsuc: { required: true },
			otra_empresa: { required: true },
			estado: { required: true },
			tippag: { required: true },
			codban: { required: true },
		},
		messages: {
			cedtra: {required: 'El campo cédula es obligatorio.'},
			tipdoc: {required: 'El campo tipo documento es obligatorio.'},
			priape: {required: 'El campo apellido paterno es obligatorio.'},
			prinom: {required: 'El campo nombre es obligatorio.'},
		}
	}
}
