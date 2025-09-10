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
			cedcon: '',
			cedtra: '',
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
			cedcon: { required: true, minlength: 5, maxlength: 20 },
			cedtra: { required: true },
			tipdoc: { required: true },
			priape: { required: true },
			prinom: { required: true },
			fecnac: { required: true },
			ciunac: { required: true },
			sexo: { required: true },
			estciv: { required: true },
			comper: { required: true },
			codzon: { required: true },
			tipviv: { required: true },
			telefono: { required: true },
			celular: { required: true },
			email: { required: false, email: true },
			nivedu: { required: true },
			codocu: { required: true },
		},
		messages: {
			cedcon: { required: 'Se requiere del campo cedula conyuge' },
			cedtra: { required: 'Se requiere del campo cedula trabajador' },
			tipdoc: { required: 'Se requiere del campo tipo documento' },
			priape: { required: 'Se requiere del campo primer apellido' },
			prinom: { required: 'Se requiere del campo primer nombre' },
			fecnac: { required: 'Se requiere del campo fecha nacimiento' },
			ciunac: { required: 'Se requiere del campo ciudad nacimiento' },
			sexo: { required: 'Se requiere del campo sexo' },
			estciv: { required: 'Se requiere del campo estado civil' },
			comper: { required: 'Se requiere del campo compañero permanente' },
			codzon: { required: 'Se requiere del campo zona' },
			tipviv: { required: 'Se requiere del campo tipo vivienda' },
			telefono: { required: 'Se requiere del campo teléfono' },
			celular: { required: 'Se requiere del campo celular' },
			email: {
				required: 'Se requiere del campo email',
				email: 'Debe tener formato de email correcto.',
			},
			nivedu: { required: 'Se requiere del campo nivel educativo' },
			codocu: { required: 'Se requiere del campo ocupación' },
		},
	};
}

export { ConyugeModel };
