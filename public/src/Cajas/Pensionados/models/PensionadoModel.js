import { RulesValidator } from '@/Componentes/Services/RulesValidator';
class PensionadoModel extends Backbone.Model {
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
		return RulesValidator(PensionadoModel.Rules.rules, attr);
	}

	static Rules = {
		nit: {
			required: true,
			minlength: 6,
		},
		razsoc: {
			required: true,
			minlength: 6,
		},
		sigla: {
			required: false,
		},
		digver: {
			required: true,
			minlength: 1,
		},
		calemp: {
			required: true,
			minlength: 1,
		},
		cedrep: {
			required: true,
			minlength: 6,
		},
		repleg: {
			required: true,
			minlength: 10,
		},
		telefono: {
			required: true,
			minlength: 7,
		},
		celular: {
			required: true,
			minlength: 10,
		},
		fax: {
			required: false,
		},
		fecini: {
			required: true,
			minlength: 10,
		},
		tottra: {
			required: true,
		},
		valnom: {
			required: true,
			minlength: 6,
		},
		dirpri: {
			required: false,
		},
		ciupri: {
			required: false,
		},
		celpri: {
			required: false,
		},
		tipemp: {
			required: true,
		},
		tipper: {
			required: true,
		},
	};
}

export { PensionadoModel };
