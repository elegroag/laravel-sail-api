import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class ConyugeModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			cedcon: null,
			cedtra: null,
			tipdoc: '',
			priape: '',
			prinom: '',
			fecnac: '',
			ciunac: '',
			sexo: '',
			estciv: '',
			comper: '',
			codzon: '',
			tipviv: '',
			telefono: '',
			celular: '',
			email: '',
			nivedu: '',
			fecing: '',
			codocu: '',
			salario: '',
			fecsol: '',
			estado: void 0,
			numcue: '',
			codban: '',
			tippag: '',
		};
	}

	validate(attr = {}) {
		return RulesValidator(ConyugeModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			cedcon: { required: true, rangelength: [5, 18] },
			cedtra: { required: true, rangelength: [5, 18] },
			tipdoc: { required: true, rangelength: [1, 3] },
			priape: { required: true },
			prinom: { required: true },
			fecnac: { required: true, date: true },
			ciunac: { required: true },
			sexo: { required: true },
			estciv: { required: true },
			comper: { required: true },
			codzon: { required: true },
			tipviv: { required: true },
			telefono: { required: true, number: true, rangelength: [7, 10] },
			celular: { required: true, number: true, rangelength: [7, 10] },
			email: { required: false },
			nivedu: { required: true },
			codocu: { required: true },
		},
		messages: {
			cedcon: 'Se requiere del campo cedula',
			cedtra: 'Se requiere del campo cedula',
			tipdoc: 'Se requiere del campo tipo documento',
			priape: 'Se requiere del campo primer apellido',
			prinom: 'Se requiere del campo primer nombre',
			fecnac: 'Se requiere del campo fecha nacimiento',
			ciunac: 'Se requiere del campo ciudad',
			sexo: 'Se requiere del campo sexo',
			estciv: 'Se requiere del campo estado civil',
			comper: 'Se requiere del campo compañero permanente',
			codzon: 'Se requiere del campo zona',
			tipviv: 'Se requiere del campo tipo vivienda',
			telefono: 'Se requiere del campo télefono',
			celular: 'Se requiere del campo celular',
			email: 'Se requiere del campo email',
			nivedu: 'Se requiere del campo nivel educativo',
			codocu: 'Se requiere del campo ocupación',
		},
	};

	static changeTipdoc(index = 1) {
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

	static changeRuleProperty(transfer = {}) {
		const { rule, prop, value } = transfer;
		ConyugeModel.Rules.rules[rule][prop] = value;
	}

	static changeRulesProperty(transfer = []) {
		_.each(transfer, (row) => ConyugeModel.changeRuleProperty(row));
	}
}

export { ConyugeModel };
