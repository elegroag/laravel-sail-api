import { AportesModel } from '@/Componentes/Models/AportesModel';

class AportesCollection extends Backbone.Collection {
	constructor(options) {
		super(options);
	}

	get model() {
		return AportesModel;
	}
}

export { AportesCollection };
