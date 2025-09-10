import { RulesValidator } from '@/Componentes/Services/RulesValidator';
class BeneficiarioAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			tippag: '',
			codcue: '',
			numcue: '',
			tipcue: '',
			fecafi: '',
			recsub: '',
			numhij: '',
			estado: void 0,
			nota_aprobar: '',
		};
	}

	validate(attr = {}) {
		return RulesValidator(BeneficiarioAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			tippag: { required: true },
			codcue: { required: false },
			numcue: { required: false },
			tipcue: { required: false },
			fecafi: { required: true },
			recsub: { required: true },
			numhij: { required: true },
			nota_aprobar: { required: true },
		},
		messages: {
			tippag: { required: 'Se requiere del campo tipo pago' },
			codcue: { required: 'Se requiere del campo cuenta' },
			numcue: { required: 'Se requiere del campo numero cuenta' },
			tipcue: { required: 'Se requiere del campo tipo cuenta' },
			fecafi: { required: 'Se requiere del campo fecha afiliación' },
			recsub: { required: 'Se requiere del campo resolucion' },
			numhij: { required: 'Se requiere del campo número hijo' },
			nota_aprobar: { required: 'Se requiere del campo nota' },
		},
	};
}

export { BeneficiarioAprobarModel };
