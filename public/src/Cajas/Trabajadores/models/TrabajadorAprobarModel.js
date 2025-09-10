import { RulesValidator } from '@/Componentes/Services/RulesValidator';
export default class TrabajadorAprobarModel extends Backbone.Model {
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
			codsuc: '',
			codlis: '',
			vendedor: '',
			empleador: '',
			numcue: '',
			tipcue: '',
			fecafi: '',
			giro: '',
			estado: void 0,
			codban: 0,
			tippag: 'T',
			nota_aprobar: null,
		};
	}

	validate(attr = {}) {
		return RulesValidator(TrabajadorAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			id: { required: true },
			codsuc: { required: true },
			codlis: { required: true },
			vendedor: { required: true },
			empleador: { required: true },
			tippag: { required: true },
			banco: { required: false },
			numcue: { required: false },
			tipcue: { required: false },
			fecafi: { required: true },
			fecapr: { required: true },
			giro: { required: true },
			nota_aprobar: { required: true },
		},
		messages: {
			codsuc: { required: 'Se requiere del campo sucursal' },
			codlis: { required: 'Se requiere del campo lista' },
			vendedor: { required: 'Se requiere del campo vendedor' },
			empleador: { required: 'Se requiere del campo empleador' },
			tippag: { required: 'Se requiere del campo tipo pago' },
			banco: { required: 'Se requiere del campo banco' },
			numcue: { required: 'Se requiere del campo número cuenta' },
			tipcue: { required: 'Se requiere del campo tipo cuenta' },
			fecafi: { required: 'Se requiere del campo fecha afiliación', date:'Se requiere del campo fecha valida en formato YYYY-MM-DD' },
			fecapr: { required: 'Se requiere del campo fecha aprobación', date:'Se requiere del campo fecha valida en formato YYYY-MM-DD' },
			giro: { required: 'Se requiere del campo giro' },
			nota_aprobar: { required: 'Se requiere del campo nota' },
		},
	};
}
