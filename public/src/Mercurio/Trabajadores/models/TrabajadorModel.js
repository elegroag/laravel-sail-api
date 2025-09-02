import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class TrabajadorModel extends Backbone.Model {
	constructor(options = {}) {
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
			tippag: void 0,
			otra_empresa: '',
			estado: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(TrabajadorModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			nit: { required: true, rangelength: [5, 18] },
			razsoc: { required: true, minlength: 5 },
			cedtra: { required: true, rangelength: [5, 18] },
			tipdoc: { required: true },
			priape: { required: true, minlength: 4 },
			prinom: { required: true, minlength: 4 },
			fecnac: { required: true },
			ciunac: { required: true, minlength: 4 },
			sexo: { required: true },
			estciv: { required: true },
			cabhog: { required: true },
			direccion: { required: true, minlength: 4 },
			dirlab: { required: true },
			telefono: { required: true, minlength: 7, rangelength: [7, 10] },
			celular: { required: true, minlength: 10, number: true },
			email: { required: true, email: true },
			fecing: { required: true, date: true },
			salario: { required: true },
			captra: { required: true },
			tipdis: { required: true },
			nivedu: { required: true },
			rural: { required: true, minlength: 1 },
			horas: { required: true, minlength: 2 },
			tipcon: { required: true },
			vivienda: { required: true, minlength: 1 },
			tipafi: { required: true, minlength: 1 },
			cargo: { required: true, minlength: 1 },
			autoriza: { required: true, minlength: 1 },
			trasin: { required: true },
			tipjor: { required: true, minlength: 1 },
			comision: { required: true },
			ruralt: { required: true },
			orisex: { required: true, minlength: 1 },
			facvul: { required: true, minlength: 1 },
			peretn: { required: true, minlength: 1 },
			codsuc: { required: true, minlength: 1 },
			otra_empresa: { required: false },
		},
		messages: {
			nit: { required: 'Se requiere de nit' },
			razsoc: { required: 'Se requiere de razon social' },
			cedtra: { required: 'Se requiere cedula trabajador' },
			tipdoc: { required: 'Se requiere de tipo documento' },
			priape: { required: 'Se requiere de primer apellido' },
			prinom: { required: 'Se requiere de primer nombre' },
			fecnac: { required: 'Se requiere de fecha nacimiento' },
			ciunac: { required: 'Se requiere de ciunac' },
			sexo: { required: 'Se requiere de sexo' },
			estciv: { required: 'Se requiere de estado civil' },
			cabhog: { required: 'Se requiere de cabeza de hogar' },
			direccion: { required: 'Se requiere de dirección' },
			dirlab: { required: 'Se requiere de dirección laboral' },
			telefono: { required: 'Se requiere de télefono' },
			celular: { required: 'Se requiere de celular' },
			email: { required: 'Se requiere de email' },
			fecing: { required: 'Se requiere de fecha ingreso' },
			salario: { required: 'Se requiere de salario' },
			captra: { required: 'Se requiere de capacidad trabajar' },
			tipdis: { required: 'Se requiere de tipo discapacidad' },
			nivedu: { required: 'Se requiere de nivel educativo' },
			rural: { required: 'Se requiere de rural' },
			horas: { required: 'Se requiere de horas' },
			tipcon: { required: 'Se requiere de tipo contrato' },
			vivienda: { required: 'Se requiere de vivienda' },
			tipafi: { required: 'Se requiere de tipo afiliación' },
			cargo: { required: 'Se requiere de cargo' },
			autoriza: { required: 'Se requiere de autoriza' },
			trasin: { required: 'Se requiere de trasin' },
			tipjor: { required: 'Se requiere de tipo jornada' },
			comision: { required: 'Se requiere de comision' },
			ruralt: { required: 'Se requiere de rural' },
			orisex: { required: 'Se requiere de orientación sexual' },
			facvul: { required: 'Se requiere de factor vulnerabilidad' },
			peretn: { required: 'Se requiere de pertenencia etnica' },
			codsuc: { required: 'Se requiere de código sucursal' },
			otra_empresa: { required: 'Se requiere de otra empresa' },
		},
	};

	static changeRuleProperty(transfer = {}) {
		const { rule, prop, value } = transfer;
		TrabajadorModel.Rules.rules[rule][prop] = value;
	}

	static changeRulesProperty(transfer = []) {
		_.each(transfer, (row) => TrabajadorModel.changeRuleProperty(row));
	}
}

export { TrabajadorModel };
