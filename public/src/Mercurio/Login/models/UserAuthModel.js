import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class UserAuthModel extends Backbone.Model {
	constructor(options = {}) {
		super(options);
	}

	get idAttribute() {
		return 'documento';
	}

	get defaults() {
		return {
			documento: void 0,
			tipo: void 0,
			clave: void 0,
			coddoc: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(UserAuthModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			tipafi: { required: true },
			clave: { required: true, maxlength: 45 },
			documento: {
				required: true,
				minlength: 5,
				maxlength: 18,
			},
			coddoc: { required: true },
			tipo: { required: true, maxlength: 1 },
		},
		messages: {
			tipafi: { required: 'Se requiere de campo tipo afiliado' },
			clave: { required: 'Se requiere de campo clave' },
			documento: { required: 'Se requiere de campo documento' },
			coddoc: { required: 'Se requiere de campo tipo documento' },
			tipo: { required: 'Se requiere de campo tipo registro' },
		},
	};

	static changeRuleProperty(transfer = {}) {
		const { rule, prop, value } = transfer;
		UserAuthModel.Rules.rules[rule][prop] = value;
	}

	static changeRulesProperty(transfer = []) {
		_.each(transfer, (row) => UserAuthModel.changeRuleProperty(row));
	}
}

export { UserAuthModel };
