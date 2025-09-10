import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class PensionadoAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			actapr: null,
			codind: null,
			todmes: '',
			tipemp: '',
			tipsoc: null,
			tipapo: null,
			fecafi: '',
			diahab: null,
			feccap: '',
			nota_aprobar: '',
			estado: null,
			codban: 0,
			tippag: 'T',
			codsuc: null,
		};
	}

	validate(attr = {}) {
		return RulesValidator(PensionadoAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			actapr: { required: true },
			codind: { required: true },
			tipemp: { required: true },
			tipsoc: { required: true },
			tipapo: { required: true },
			subpla: { required: false },
			codsuc: { required: true },
			fecafi: { required: true, date: true },
			feccap: { required: true, date: true },
			diahab: { required: true, number: true },
			todmes: { required: true },
			nota_aprobar: { required: true, minlength: 10 },
		},
		messages: {
			actapr: 'El campo es obligatorio.',
			codind: 'El campo es obligatorio.',
			tipemp: 'El campo es obligatorio.',
			tipsoc: 'El campo es obligatorio.',
			tipapo: 'El campo es obligatorio.',
			subpla: 'El campo es obligatorio.',
			fecafi: 'El campo es obligatorio.',
			feccap: 'El campo es obligatorio.',
			diahab: 'El campo es obligatorio.',
			todmes: 'El campo es obligatorio.',
			nota: 'El campo es obligatorio.',
			codban: 'El campo es obligatorio.',
			tippag: 'El campo es obligatorio.',
			codsuc: 'El campo es obligatorio.',
		},
	};
}

export { PensionadoAprobarModel };
