import { $App } from '@/App.js';
import { Testeo } from '@/Core.js';
import { ControllerLogin } from './ControllerLogin.js';

class RouterLogin extends Backbone.Router {
	constructor(options = {}) {
		super({
			...options,
			routes: {
				auth: 'loginRute',
				recovery: 'recoveryRute',
				guia: 'guiaRute',
				register: 'registerRute',
				email_change: 'emailChangeRute',
				verification: 'verificationRute',
				'verify/:tipo/:coddoc/:documento': 'verificationExternal',
			},
		});
		this._bindRoutes();
		if (!this.currentApp) this.currentApp = {};
	}

	initialize() {
		this.currentApp = $App.startSubApplication(ControllerLogin);
	}

	loginRute() {
		this.currentApp.login();
	}

	recoveryRute() {
		this.currentApp.recovery();
	}

	guiaRute() {
		this.currentApp.guia();
	}

	registerRute() {
		this.currentApp.register();
	}

	emailChangeRute() {
		this.currentApp.emailChange();
	}

	verificationRute() {
		const _props = sessionStorage.getItem('miTokenAuth');
		if (_.isUndefined(_props) == true || _.isNull(_props) == true || _props == '') {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			return this.navigate('auth', { trigger: true, replace: true });
		}

		let decode;
		let alg = 0;
		let flag;
		try {
			decode = atob(_props);
			flag = JSON.parse(decode);
		} catch (errors) {
			alg++;
			$App.trigger('alert:warning', {
				message: 'Los parametros no son validos para continuar',
			});
			return this.navigate('auth', { trigger: true, replace: true });
		}
		if (alg == 0) {
			let err;
			if (flag) {
				err = Testeo.identi({
					attr: flag.documento,
					target: 'documento',
					label: 'identificación',
					min: 6,
					max: 19,
				});
				if (err) {
					$App.trigger('alert:warning', {
						message: 'El documento no es valido para continuar',
					});
					return this.navigate('auth', { trigger: true, replace: true });
				}

				err = Testeo.identi({
					attr: flag.coddoc,
					target: 'coddoc',
					label: 'tipo documneto',
					min: 1,
					max: 3,
				});

				if (err) {
					$App.trigger('alert:warning', {
						message: 'El tipo documento no es valido para continuar',
					});
					this.navigate('auth', { trigger: true, replace: true });
					return false;
				}

				err = Testeo.vacio({
					attr: flag.tipo,
					target: 'tipo',
					label: 'tipo afiliado',
				});

				if (
					!(
						flag.tipo == 'P' ||
						flag.tipo == 'T' ||
						flag.tipo == 'E' ||
						flag.tipo == 'I' ||
						flag.tipo == 'O' ||
						flag.tipo == 'F' ||
						flag.tipo == 'S'
					)
				) {
					err = true;
				}

				if (err) {
					$App.trigger('alert:warning', {
						message: 'El tipo afilición no es valido para continuar',
					});
					return this.navigate('auth', { trigger: true, replace: true });
				}
				this.currentApp.verification(flag);
			}
		}
	}

	verificationExternal(tipo = undefined, coddoc = undefined, documento = undefined) {
		if (
			_.isUndefined(tipo) == true ||
			_.isUndefined(coddoc) == true ||
			_.isUndefined(documento) == true
		) {
			$App.trigger('alert:error', {
				message: 'No hay una solicitud en proceso para continuar.',
			});
			this.navigate('auth', { trigger: true, replace: true });
			return false;
		}
		let err = Testeo.identi({
			attr: documento,
			target: 'documento',
			label: 'identificación',
			min: 6,
			max: 19,
		});
		if (err) {
			$App.trigger('alert:warning', {
				message: 'El documento no es valido para continuar',
			});
			this.navigate('auth', { trigger: true, replace: true });
			return false;
		}

		err = Testeo.identi({
			attr: coddoc,
			target: 'coddoc',
			label: 'tipo documneto',
			min: 1,
			max: 3,
		});

		if (err) {
			$App.trigger('alert:warning', {
				message: 'El tipo documento no es valido para continuar',
			});
			this.navigate('auth', { trigger: true, replace: true });
			return false;
		}

		err = Testeo.vacio({
			attr: tipo,
			target: 'tipo',
			label: 'tipo afiliado',
		});

		if (
			!(
				tipo == 'P' ||
				tipo == 'T' ||
				tipo == 'E' ||
				tipo == 'I' ||
				tipo == 'O' ||
				tipo == 'F' ||
				tipo == 'S'
			)
		) {
			err = true;
		}

		if (err) {
			$App.trigger('alert:warning', {
				message: 'El tipo afilición no es valido para continuar',
			});
			this.navigate('auth', { trigger: true, replace: true });
			return false;
		}

		const miTokenAuth = btoa(
			JSON.stringify({
				documento,
				coddoc,
				tipo,
			}),
		);
		sessionStorage.setItem('miTokenAuth', miTokenAuth);
		this.navigate('verification', { trigger: true, replace: true });
		return false;
	}
}

export { RouterLogin };
