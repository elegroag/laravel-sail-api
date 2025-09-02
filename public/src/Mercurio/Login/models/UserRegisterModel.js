import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class UserRegisterModel extends Backbone.Model {
	constructor(options = {}) {
		super(options);
	}

	get idAttribute() {
		return 'cedrep';
	}

	get defaults() {
		return {
			cedrep: null,
			tipo: '',
			nit: null,
			razsoc: '',
			codciu: '',
			repleg: '',
			tipsoc: '',
			tipafi: '',
			coddoc: 0,
			tipper: '',
			email: '',
			calemp: '',
			coddocrepleg: '',
		};
	}

	validate(attr = {}) {
		return RulesValidator(UserRegisterModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			tipo: { required: true, minlength: 1 },
			nit: { required: true, minlength: 5, maxlength: 16 },
			razsoc: { required: true },
			codciu: { required: true },
			cedrep: { required: true, minlength: 5, maxlength: 16 },
			repleg: { required: true, maxlength: 80 },
			tipsoc: { required: true, minlength: 1 },
			coddoc: { required: true, minlength: 1 },
			tipper: { required: true, minlength: 1 },
			email: { required: true, email: true },
			calemp: { required: false, minlength: 1 },
			coddocrepleg: { required: true, minlength: 1 },
		},
		messages: {
			tipo: {
				required: 'Se requere del campo tipo',
				minlength: 'Se requiere el valor minimo para tipo',
			},
			nit: {
				required: 'Se requere del campo nit',
				minlength: 'Se requiere el valor minimo 5 para nit',
			},
			razsoc: { required: 'Se requere del campo razon social' },
			codciu: { required: 'Se requere del campo ciudad' },
			cedrep: { required: 'Se requere del campo cedula' },
			repleg: { required: 'Se requere del campo nombre' },
			tipsoc: { required: 'Se requere del campo tipo sociedad' },
			coddoc: { required: 'Se requere del campo tipo documento' },
			tipper: { required: 'Se requere del campo tipo persona' },
			email: { required: 'Se requere del campo email' },
			coddocrepleg: { required: 'Se requere del campo tipo documento' },
		},
	};

	static changeRuleProperty(transfer = {}) {
		const { rule, prop, value } = transfer;
		UserRegisterModel.Rules.rules[rule][prop] = value;
	}

	static changeRulesProperty(transfer = []) {
		_.each(transfer, (row) => UserRegisterModel.changeRuleProperty(row));
	}
}

export { UserRegisterModel };
