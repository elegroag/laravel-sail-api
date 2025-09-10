import IndependienteModel from '../models/IndependienteModel';

export default class IndependientesCollection extends Backbone.Collection {
	constructor(options) {
		super(options);
	}

	get model() {
		return IndependienteModel;
	}
}
