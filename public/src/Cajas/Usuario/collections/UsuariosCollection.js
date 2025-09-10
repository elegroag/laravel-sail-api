import { UsuarioModel } from '../models/UsuarioModel';

export class UsuariosCollection extends Backbone.Collection {
	constructor(options) {
		super(options);
	}

	/**
	 * @override
	 */
	// @ts-ignore
	get model() {
		return UsuarioModel;
	}
}
