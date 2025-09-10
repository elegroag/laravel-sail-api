class BeneficiarioModel extends Backbone.Model {
	constructor(options) {
		super(options);
	}

	get idAttribute() {
		return 'id';
	}

	get defaults() {
		return {
			id: null,
			tipdoc: '',
			numdoc: '',
			priape: '',
			prinom: '',
			fecnac: '',
			sexo: '',
			parent: '',
			huerfano: '',
			tiphij: '',
			nivedu: '',
			captra: '',
			tipdis: '',
			estado: void 0,
		};
	}

	validate(attr = {}, options = void 0) {
		let _err = new Array();
		return _.isEmpty(_err) === true ? null : _err;
	}

	static Rules = {
		tipdoc: { required: true },
		numdoc: { required: true, minlength: 6 },
		priape: { required: true, minlength: 5, maxlength: 34 },
		prinom: { required: true, minlength: 5, maxlength: 34 },
		fecnac: { required: true },
		sexo: { required: true },
		parent: { required: true },
		huerfano: { required: true },
		tiphij: { required: true },
		nivedu: { required: true },
		captra: { required: true },
		tipdis: { required: true },
	};
}

export { BeneficiarioModel };
