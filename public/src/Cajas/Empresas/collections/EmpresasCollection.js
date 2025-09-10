import EmpresaModel from '../models/EmpresaModel';
export default class EmpresasCollection extends Backbone.Collection {
	constructor(options) {
		super(options);
	}

	get model() {
		return EmpresaModel;
	}
}
