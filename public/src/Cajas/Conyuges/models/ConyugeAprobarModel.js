import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class ConyugeAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			nota: '',
			cedtra: '',
			nit: '',
			tippag: '',
			banco: '',
			numcue: '',
			tipcue: '',
			fecafi: '',
			giro: '',
			estado: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(ConyugeAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			id: { required: true },
			tippag: { required: true },
			codcue: { required: false },
			numcue: { required: false },
			tipcue: { required: false },
			banco: { required: false },
			fecafi: { required: true },
			recsub: { required: true },
		},
		messages: {
			tippag: { required: 'Se requiere del campo tipo pago' },
			banco: { required: 'Se requiere del campo banco' },
			codcue: { required: 'Se requiere del campo cuenta' },
			numcue: { required: 'Se requiere del campo número cuenta' },
			tipcue: { required: 'Se requiere del campo tipo cuenta' },
			fecafi: { required: 'Se requiere del campo fecha afiliación' },
			recsub: { required: 'Se requiere del campo resolución' },
		},
	};
}

export { ConyugeAprobarModel };
