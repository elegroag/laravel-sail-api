import { $App } from '@/App';
import { Region } from '@/Common/Region';
import LoginView from './views/LoginView';
import LayoutLogin from './views/LayoutLogin';
import InfoView from './views/InfoView';

export default class Login {
	#App = null;
	#region = null;
	#layout = null;

	constructor(options = {}) {
		_.extend(this, Backbone.Events);
		sessionStorage.setItem('miTokenAuth', '');
		this.#App = options.App || $App;
		this.#region = options.region || null;
	}

	main() {
		this.#layout = new LayoutLogin({
			model: {
				useInfo: true,
			},
		});
		this.#region.show(this.#layout);

		const infoView = new InfoView({
			model: {
				title: 'Inicio de sesión, consulta Y gestión',
				text: 'Por favor, ingresa los datos solicitados para continuar.',
			},
		});
		this.#layout.getRegion('info').show(infoView);

		const view = new LoginView({ collection: this.#App.Collections.formParams });
		this.#layout.getRegion('login').show(view);

		$('#render_login').fadeIn('fast', () => $('#render_left_content').fadeIn('slow'));
		$('#render_sesion').fadeIn();
		$('#render_register').fadeOut();
	}

	destroy() {
		this.stopListening();
		if (this.#region && this.#region instanceof Region) this.#region.remove();
	}
}
