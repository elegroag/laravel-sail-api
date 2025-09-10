import AportesModel from '../models/AportesModel';

export default class AportesCollection extends Backbone.Collection {
	constructor(options) {
		super(options);
	}

	get model() {
		return AportesModel;
	}
}
