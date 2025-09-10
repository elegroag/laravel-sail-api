import { ModelView } from '@/Common/ModelView';

export default class TrabajadorTrayectoriaView extends ModelView {
	constructor(parameters) {
		super(parameters);
		this.template = _.template(document.getElementById('tmp_trayectoria').innerHTML);
	}
}
