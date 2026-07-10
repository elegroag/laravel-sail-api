import { ModelView } from '@/Common/ModelView';

export class ConsultaTrabajadorView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'nucleo-tab-panel' });
		this.template = _.template(document.getElementById('templateTrabajador').innerHTML);
	}
}

export class ConsultaConyugeView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'nucleo-tab-panel' });
		this.template = _.template(document.getElementById('templateConyuge').innerHTML);
	}
}

export class ConsultaBeneficiarioView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'nucleo-tab-panel' });
		this.template = _.template(document.getElementById('templateBeneficiario').innerHTML);
	}
}
