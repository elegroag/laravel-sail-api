'use strict';

class DocumentoModel extends Backbone.Model {
	constructor(options = {}) {
		super(options);
	}

	get idAttribute() {
		return 'coddoc';
	}

	get defaults() {
		return {
			coddoc: null,
			puede_borrar: false,
			tipopc: void 0,
			tipsoc: void 0,
			obliga: null,
			auto_generado: 0,
			id: null,
			detalle: void 0,
			diponible: void 0,
			corrige: false,
		};
	}
}

export { DocumentoModel };
