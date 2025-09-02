import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class UserRecoveryModel extends Backbone.Model {
	constructor(options = {}) {
		super(options);
	}

	get idAttribute() {
		return 'documento';
	}

	get defaults() {
		return {
			documento: null,
			tipo: void 0,
			coddoc: void 0,
			email: void 0,
		};
	}

	validate(attr = {}) {
		return RulesValidator(UserRecoveryModel.Rules.rules, attr);
	}

	static Rules = {
		rules: {
			documento: {
				required: true,
				minlength: 5,
				maxlength: 18,
			},
			coddoc: { required: true },
			tipo: { required: true, maxlength: 1 },
			tipafi: { required: false, maxlength: 1 },
			email: { required: true },
			telefono: { required: false },
			novedad: { required: false },
		},
		messages: {
			documento: { required: 'Se requiere campo documento' },
			coddoc: { required: 'Se requiere campo tipo documento' },
			tipo: { required: 'Se requiere campo tipo' },
			email: { required: 'Se requiere campo email' },
			novedad: { required: 'Se requiere campo novedad' },
			telefono: { required: 'Se requiere campo telefono' },
			tipafi: { required: 'Se requiere campo tipo afiliado' },
		},
	};

	static changeRuleProperty(transfer = {}) {
		const { rule, prop, value } = transfer;
		UserRecoveryModel.Rules.rules[rule][prop] = value;
	}

	static changeRulesProperty(transfer = []) {
		_.each(transfer, (row) => UserRecoveryModel.changeRuleProperty(row));
	}
}

export { UserRecoveryModel };
