import { $App } from '@/App';
import { Controller } from '@/Common/Controller';
import Verification from './Verification';
import EmailChange from './EmailChange';
import Login from './Login';
import Register from './Register';
import Recovery from './Recovery';

class ControllerLogin extends Controller {
	constructor(options = {}) {
		super(options);
		this.App = options.App || $App;
		this.App.Collections.formParams = null;
	}

	login() {
		const app = this.startController(Login);
		if (_.isNull(this.App.Collections.formParams)) {
			this.__paramsLogin({
				callback: (response) => {
					if (response) app.main();
				},
			});
		} else {
			app.main();
		}
	}

	recovery() {
		const app = this.startController(Recovery);
		if (_.isNull(this.App.Collections.formParams)) {
			this.__paramsLogin({
				callback: (response) => {
					if (response) app.main();
				},
			});
		} else {
			app.main();
		}
	}

	guia() {
		$('#render_register').fadeIn();
		$('#render_sesion').fadeOut();
	}

	register() {
		const app = this.startController(Register);
		if (_.isNull(this.App.Collections.formParams)) {
			this.__paramsLogin({
				callback: (response) => {
					if (response) app.main(this.formComponents);
				},
			});
		} else {
			app.main(this.formComponents);
		}
	}

	verification(params) {
		const app = this.startController(Verification);
		app.main(params);
	}

	emailChange() {
		const app = this.startController(EmailChange);
		if (_.isNull(this.App.Collections.formParams)) {
			this.__paramsLogin({
				callback: (response) => {
					if (response) app.main();
				},
			});
		} else {
			app.main();
		}
	}

	__paramsLogin(transfer = {}) {
		const { callback, silent = false } = transfer;

		this.App.trigger('syncro', {
			url: this.App.url('paramsLogin'),
			data: {},
			silent,
			callback: (response = {}) => {
				if (!_.isEmpty(response)) {
					if (response.success) {
						if (_.isNull(this.App.Collections.formParams))
							this.App.Collections.formParams = [];
						_.extend(this.App.Collections.formParams, response.data);
						this.formComponents = response.components;
						return callback !== false ? callback(response.msj) : '';
					}
				}
				return callback !== false ? callback(false) : '';
			},
		});
	}
}

export { ControllerLogin };
