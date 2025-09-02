import { ModelView } from '@/Common/ModelView';
import { $App } from '@/App';

export class ConsultaTrabajadorView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'row', onRender: () => this.afterRender() });
		this.$App = $App;
		this.template = _.template(document.getElementById('templateTrabajador').innerHTML);
	}

	afterRender() {
		console.log('afterRender');
	}
}

export class ConsultaConyugeView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'row', onRender: () => this.afterRender() });
		this.$App = $App;
		this.template = _.template(document.getElementById('templateConyuge').innerHTML);
	}

	afterRender() {
		console.log('afterRender');
	}
}

export class ConsultaBeneficiarioView extends ModelView {
	constructor(options = {}) {
		super({ ...options, className: 'row', onRender: () => this.afterRender() });
		this.$App = $App;
		this.template = _.template(document.getElementById('templateBeneficiario').innerHTML);
	}

	afterRender() {
		console.log('afterRender');
	}
}
