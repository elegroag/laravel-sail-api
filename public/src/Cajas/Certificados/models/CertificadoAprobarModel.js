import { RulesValidator } from '@/Componentes/Services/RulesValidator';

export default class CertificadoAprobarModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			detalle: null,
		};
	}

	validate(attr = {}) {
		return RulesValidator(CertificadoAprobarModel.Rules.rules, attr);
	}

	static Rules = {
		rules:{
			cedcon: { required: true, minlength: 5, maxlength: 20 },
		},
		messages:{
			cedcon: { required: 'Se requiere del campo cédula', minlength: 'Se requiere del campo cédula', maxlength: 'Se requiere del campo cédula' },
		}
	};
}
