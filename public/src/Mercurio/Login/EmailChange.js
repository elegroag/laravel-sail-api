import { $App } from '@/App';
import { Region } from '@/Common/Region';
import LayoutLogin from './views/LayoutLogin';
import EmailChangeView from './views/EmailChangeView';
import InfoView from './views/InfoView';

export default class EmailChange {
	#region = null;
	#layout = null;
	#App = null;

	constructor(options = {}) {
		_.extend(this, Backbone.Events);
		this.#App = options.App || $App;
		this.#region = options.region;

		sessionStorage.setItem('miTokenAuth', '');

		this.#layout = new LayoutLogin({
			model: {
				useInfo: true,
			},
		});
		this.#region.show(this.#layout);
	}

	main() {
		const infoView = new InfoView({
			model: {
				title: 'Cambio de correo electrónico',
				text: 'Por favor, ingresa los datos solicitados para continuar.',
			},
		});
		this.#layout.getRegion('info').show(infoView);

		const view = new EmailChangeView({ collection: this.#App.Collections.formParams });
		this.#layout.getRegion('recovery').show(view);
		$('#render_info').fadeIn();
		$('#render_recovery').fadeIn();
		$('#render_register').fadeOut();
		$('#render_sesion').fadeIn();
	}

	destroy() {
		this.stopListening();
		if (this.#region && this.#region instanceof Region) this.#region.remove();
	}
}
