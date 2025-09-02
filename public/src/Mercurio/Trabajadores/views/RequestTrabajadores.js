import { CollectionView } from '@/Componentes/Collections/CollectionView';
import { ModelView } from '@/Componentes/Views/ModelView';

class RequestTrabajador extends ModelView {
	constructor(options = {}) {
		super({ ...options, tagName: 'tr', template: '#tmp_row' });
	}
}

class RequestTrabajadores extends CollectionView {
	constructor(options = {}) {
		super({ ...options, template: '#tmp_table' });
		//if (_.isUndefined(this.el) === true) this.el = '#contentTableRequest';
		this.modelView = RequestTrabajador;
	}

	get events() {
		return {
			'click #cancel': 'cancel',
		};
	}
}

export { RequestTrabajador, RequestTrabajadores };
