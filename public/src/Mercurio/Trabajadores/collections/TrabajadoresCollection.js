import { TrabajadorNominaModel } from '@/Componentes/Models/TrabajadorNominaModel';
import { TrabajadorModel } from '../models/TrabajadorModel';

class TrabajadoresCollection extends Backbone.Collection {
	constructor() {
		super();
	}

	get url() {
		return '/Mercurio/trajadores/list';
	}

	get model() {
		return TrabajadorModel;
	}
}

class TraNomCollection extends Backbone.Collection {
	constructor() {
		super();
	}

	get model() {
		return TrabajadorNominaModel;
	}
}

export { TraNomCollection, TrabajadoresCollection };
