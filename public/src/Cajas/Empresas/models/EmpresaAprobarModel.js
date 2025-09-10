import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class EmpresaAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			nit: void 0,
			tipdur: '',
			actapr: '',
			codind: '',
			todmes: '',
			forpre: '',
			pymes: '',
			contratista: '',
			tipemp: '',
			tipsoc: '',
			tipapo: '',
			ofiafi: '',
			colegio: '',
			subpla: '',
			fecafi: '',
			feccap: '',
			diahab: '',
			nota_aprobar: '',
			estado: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(EmpresaAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			tipdur: { required: true },
			actapr: { required: true },
			codind: { required: true },
			todmes: { required: true },
			forpre: { required: true },
			pymes: { required: true },
			contratista: { required: true },
			tipemp: { required: true },
			tipsoc: { required: true },
			tipapo: { required: true },
			ofiafi: { required: true },
			colegio: { required: true },
			codsuc: { required: true },
			fecafi: { required: true, date: true },
			feccap: { required: true, date: false },
			diahab: { required: true },
			nota_aprobar: { required: true },
			fecapr: { required: true, date: true },
		},
		messages: {
			tipdur: { required: 'Se requiere del campo tipdur' },
			actapr: { required: 'Se requiere del campo acta' },
			codind: { required: 'Se requiere del campo codind' },
			todmes: { required: 'Se requiere del campo todmes' },
			forpre: { required: 'Se requiere del campo forpre' },
			pymes: { required: 'Se requiere del campo pymes' },
			contratista: { required: 'Se requiere del campo contratista' },
			tipemp: { required: 'Se requiere del campo tipo empresa' },
			tipsoc: { required: 'Se requiere del campo tipo sociedad' },
			tipapo: { required: 'Se requiere del campo tip aportante' },
			ofiafi: { required: 'Se requiere del campo oficina afiliación' },
			colegio: { required: 'Se requiere del campo colegio' },
			codsuc: { required: 'Se requiere del campo sucursal' },
			fecafi: { required: 'Se requiere del campo fecha afiliación' },
			feccap: { required: 'Se requiere del campo fecha captura' },
			diahab: { required: 'Se requiere del campo día habíl' },
			nota_aprobar: { required: 'Se requiere del campo nota aprobar' },
			fecapr: { required: 'Se requiere fecha aprobación resolución.' },
		},
	};
}
