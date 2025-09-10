import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class FacultativoAprobarModel extends Backbone.Model {
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
			tipapo: '',
			fecafi: '',
			diahab: '',
			feccap: '',
			nota_aprobar: '',
			estado: void 0,
			codsuc: null
		};
	}

	validate(attr = {}) {
		return RulesValidator(FacultativoAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			actapr: { required: true },
			codind: { required: true },
			tipemp: { required: true },
			tipapo: { required: true },
			fecafi: { required: true },
			feccap: { required: true },
			diahab: { required: true },
			todmes: { required: true },
			nota_aprobar: { required: true },
			codsuc: { required: true }
		},
		messages: {
			actapr: {required: 'El campo acta es obligatorio.'},
			codind: {required: 'El campo indice de aportes es obligatorio.'},
			tipemp: {required: 'El campo tipo empresa es obligatorio.'},
			tipapo: {required: 'El campo es obligatorio.'},
			fecafi: {required: 'El campo fecha afiliación es obligatorio.', date: 'La fecha debe tener formato dd/mm/yyyy'},
			feccap: {required: 'El campo fecha aportes es obligatorio.', date: 'La fecha debe tener formato dd/mm/yyyy'},
			diahab: {required: 'El campo días es obligatorio.'},
			todmes: {required: 'El campo mes es obligatorio.'},
			nota_aprobar: { required: 'El campo nota aprobar es obligatorio.' },
			codsuc: {required: 'El campo sucursal es obligatorio.'},
		},
	};
}

