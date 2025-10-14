import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

class HeaderCajasView extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('tmp_header').innerHTML);
	}

	get className() {
		return 'd-flex justify-content-center';
	}

	get events() {
		return {
			'click #btListar': 'procesoListar',
			'click #btSalir': 'procesoSalir',
			'click #btFiltrar': 'procesoFiltrar',
		};
	}

	procesoFiltrar(e) {
		e.preventDefault();
		this.trigger('show:filtro', {});
	}

	procesoListar(e) {
		e.preventDefault();
		$App.router.navigate('list', { trigger: true, replace: true });
	}

	procesoSalir(e) {
		e.preventDefault();
		window.location.href = $App.url('principal/index');
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { HeaderCajasView };
