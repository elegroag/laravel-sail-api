import { ModelView } from '@/Common/ModelView';

export default class TrabajadorSisuView extends ModelView {
	constructor(parameters) {
		super(parameters);
		this.template = _.template(document.getElementById('tmp_trabajador').innerHTML);
	}
}
