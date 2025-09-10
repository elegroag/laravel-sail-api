import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class IndependienteAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			actapr: '',
			codind: '',
			todmes: '',
			tipemp: '',
			tipsoc: '',
			tipapo: '',
			fecafi: '',
			diahab: '',
			feccap: '',
			codsuc: '',
			nota_aprobar: '',
			estado: void 0,
			codban: 0,
			tippag: 'T',
		};
	}

	validate(attr = {}) {
		return RulesValidator(IndependienteAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			actapr: { required: true },
			codind: { required: true },
			tipemp: { required: true },
			tipsoc: { required: true },
			tipapo: { required: true },
			subpla: { required: false },
			fecafi: { required: true },
			feccap: { required: true },
			diahab: { required: true },
			todmes: { required: true },
			codsuc: { required: true },
			nota_aprobar: { required: true },
		},
		messages: {
			actapr: { required: 'El campo es obligatorio.' },
			codind: { required: 'El campo es obligatorio.' },
			tipemp: { required: 'El campo es obligatorio.' },
			tipsoc: { required: 'El campo es obligatorio.' },
			tipapo: { required: 'El campo es obligatorio.' },
			subpla: { required: 'El campo es obligatorio.' },
			fecafi: { required: 'El campo es obligatorio.' },
			feccap: { required: 'El campo es obligatorio.' },
			diahab: { required: 'El campo es obligatorio.' },
			todmes: { required: 'El campo es obligatorio.' },
			codsuc: { required: 'El campo es obligatorio.' },
			nota_aprobar: { required: 'El campo es obligatorio.' },
		},
	};
}
