import { ModelView } from '@/Common/ModelView';

export default class TrabajadorSalarioView extends ModelView {
	constructor(parameters) {
		super(parameters);
		this.template = _.template(document.getElementById('tmp_salario').innerHTML);
	}
}
