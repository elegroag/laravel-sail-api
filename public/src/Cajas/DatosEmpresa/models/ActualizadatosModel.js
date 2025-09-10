import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class ActualizadatosModel extends Backbone.Model {

	constructor(options = {}) {
		super(options);
		this.on('change:tipdoc', (model) => {
			let coddocrepleg = ActualizadatosModel.changeTipdoc(model.get('tipdoc'));
			model.set('coddocrepleg', coddocrepleg);
		});
	}

	//@ts-ignore
	get idAttribute() {
		return 'id';
	}

	//@ts-ignore
	get defaults() {
		return {
			id: null,
			codsuc: void 0,
			nit: void 0,
			razsoc: '',
			sigla: '',
			digver: void 0,
			calemp: '',
			cedrep: '',
			repleg: '',
			telefono: void 0,
			celular: void 0,
			email: '',
			fecini: void 0,
			tottra: void 0,
			valnom: void 0,
			tipemp: '',
			tipper: '',
			priape: '',
			prinom: '',
			segnom: '',
			segape: '',
			codact: void 0,
			coddocrepleg: void 0,
			coddoc: void 0,
			estado: void 0,
			tipdoc: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(ActualizadatosModel.Rules.rules, attr);
	}

	static Rules = {
		rules:{
			nit: { required: true, minlength: 6 },
			razsoc: { required: true, minlength: 5 },
			sigla: { required: false },
			digver: { required: true, minlength: 1 },
			calemp: { required: true, minlength: 1 },
			cedrep: { required: true, minlength: 6 },
			repleg: { required: true, minlength: 6 },
			telefono: { required: true, minlength: 7 },
			celular: { required: true, minlength: 10 },
			email: { required: true },
			fecini: { required: true, minlength: 10 },
			tottra: { required: true },
			valnom: { required: true, minlength: 6 },
			dirpri: { required: false },
			ciupri: { required: true },
			celpri: { required: false },
			emailpri: { required: false },
			tipemp: { required: true },
			tipper: { required: true },
			prinom: { required: true },
			priape: { required: true },
			codsuc: { required: true },
			coddoc: { required: true },
			coddocrepleg: { required: true },
			tipdoc: { required: true },
		},
		messages:{
			nit: { required: 'Se requiere del campo nit' },
			digver: { required: 'Se requiere del campo digito verificador' },
			calemp: { required: 'Se requiere del campo calemp' },
			cedrep: { required: 'Se requiere del campo cedrep' },
			repleg: { required: 'Se requiere del campo repleg' },
			telefono: { required: 'Se requiere del campo telefono' },
			celular: { required: 'Se requiere del campo celular' },
			email: { required: 'Se requiere del campo email' },
			fecini: { required: 'Se requiere del campo fecini' },
			tottra: { required: 'Se requiere del campo tottra' },
			valnom: { required: 'Se requiere del campo valnom' },
			tipemp: { required: 'Se requiere del campo tipemp' },
			tipper: { required: 'Se requiere del campo tipper' },
			prinom: { required: 'Se requiere del campo prinom' },
			priape: { required: 'Se requiere del campo priape' },
			coddoc: { required: 'Se requiere del campo coddoc' },
			coddocrepleg: { required: 'Se requiere del campo coddocrepleg' },
			tipdoc: { required: 'Se requiere del campo tipdoc' },
			dirpri: { required: 'Se requiere del campo dirpri' },
			ciupri: { required: 'Se requiere del campo ciupri' },
			celpri: { required: 'Se requiere del campo celpri' },
			emailpri: { required: 'Se requiere del campo emailpri' },
			segnom: { required: 'Se requiere del campo segnom' },
			segape: { required: 'Se requiere del campo segape' },
			codact: { required: 'Se requiere del campo codact' }
		}
	};

	static changeTipdoc(index) {
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
}
