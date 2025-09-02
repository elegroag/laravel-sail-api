import { $App } from '@/App';
import { Region } from '@/Common/Region';
import LayoutLogin from './views/LayoutLogin';
import RecoveryView from './views/RecoveryView';
import InfoView from './views/InfoView';

export default class Recovery {
	#App = null;
	#region = null;
	#layout = null;

	constructor(options = {}) {
		_.extend(this, Backbone.Events);
		sessionStorage.setItem('miTokenAuth', '');
		this.#App = options.App || $App;
		this.#region = options.region;
		this.#layout = new LayoutLogin({
			model: {
				useInfo: true,
			},
		});
		this.#region.show(this.#layout);
	}

	main() {
		const view = new RecoveryView({ collection: this.#App.Collections.formParams });
		this.#layout.getRegion('recovery').show(view);

		const infoView = new InfoView();
		this.#layout.getRegion('info').show(infoView);

		$('#render_recovery').fadeIn('fast', () => {
			$('#render_left_content').fadeIn('slow');
		});
		$('#render_register').fadeOut();
		$('#render_sesion').fadeIn();
	}

	destroy() {
		this.stopListening();
		if (this.#region && this.#region instanceof Region) this.#region.remove();
	}
}
