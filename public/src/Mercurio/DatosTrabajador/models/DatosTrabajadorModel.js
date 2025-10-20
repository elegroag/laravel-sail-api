import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class DatosTrabajadorModel extends Backbone.Model {
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
			salario: 0,
			captra: '',
			tipdis: '',
			nivedu: '',
			rural: '',
			horas: 0,
			tipcon: '',
			vivienda: '',
			tipafi: '',
			autoriza: '',
			trasin: '',
			tipjor: '',
			ruralt: '',
			orisex: '',
			facvul: '',
			peretn: '',
			codsuc: void 0,
			tippag: void 0,
			estado: void 0,
			tipact: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(DatosTrabajadorModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			cedtra: {
				required: true,
				maxlength: 16,
				minlength: 6,
			},
			priape: {
				required: true,
			},
			prinom: {
				required: true,
			},
			direccion: {
				required: true,
			},
			dirlab: {
				required: true,
			},
			telefono: {
				required: true,
			},
			celular: {
				required: true,
			},
			expedicion: {
				required: true,
			},
		},
		messages: {
			cedtra: {
				required: 'Se requiere la cédula de identidad',
				maxlength: 'La cédula de identidad debe tener máximo 16 caracteres',
				minlength: 'La cédula de identidad debe tener mínimo 6 caracteres',
			},
			priape: {
				required: 'Se requiere el apellido',
			},
			prinom: {
				required: 'Se requiere el nombre',
			},
			direccion: {
				required: 'Se requiere la dirección',
			},
			dirlab: {
				required: 'Se requiere la dirección laboral',
			},
			telefono: {
				required: 'Se requiere el teléfono',
			},
			celular: {
				required: 'Se requiere el celular',
			},
			expedicion: {
				required: 'Se requiere la fecha de expedición',
			},
		},
	};

	static changeTipdoc(index) {
		let coddoc_repleg = {
			1: 'CC',
			10: 'TMF',
			11: 'CD',
			12: 'ISE',
			13: 'V',
			14: 'PT',
			2: 'TI',
			3: 'NI',
			4: 'CE',
			5: 'NU',
			6: 'PA',
			7: 'RC',
			8: 'PEP',
			9: 'CB',
		};
		return coddoc_repleg[index];
	}
}
