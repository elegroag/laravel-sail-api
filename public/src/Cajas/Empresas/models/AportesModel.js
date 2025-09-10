export default class AportesModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			fecrec: '',
			fecsis: '',
			codsuc: '',
			periodo: '',
			nit: '',
			numero: null,
			tippla: '',
			perapo: '',
			valnom: '',
			valapo: '',
			cedtra: '',
			ingtra: null,
			novret: null,
			diatra: 0,
			salbas: 0,
			vacnom: null,
			novitg: null,
			licnom: null,
			novstc: null,
			incnom: '00',
		};
	}
}
