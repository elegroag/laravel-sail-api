import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class DatosTrabajadorModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	//@ts-ignore
	get idAttribute() {
		return 'id';
	}

	//@ts-ignore
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
		return RulesValidator(CertificadoAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules:{
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
		messages:{
			cedtra: {
				required: 'El campo cédula es requerido.',
				maxlength: 'El campo cédula debe tener máximo 16 caracteres.',
				minlength: 'El campo cédula debe tener mínimo 6 caracteres.',
			},
			priape: {
				required: 'El campo primer apellido es requerido.',
			},
			prinom: {
				required: 'El campo primer nombre es requerido.',
			},
			direccion: {
				required: 'El campo dirección es requerido.',
			},
			dirlab: {
				required: 'El campo dirección laboral es requerido.',
			},
			telefono: {
				required: 'El campo teléfono es requerido.',
			},
			celular: {
				required: 'El campo celular es requerido.',
			},
			expedicion: {
				required: 'El campo expedición es requerido.',
			},
		}
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
