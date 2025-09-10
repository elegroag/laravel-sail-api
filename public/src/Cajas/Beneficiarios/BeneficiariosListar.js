import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderListView } from '@/Cajas/HeaderListView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { BeneficiariosView } from './views/BeneficiariosView';
import { $App } from '@/App';

export default class BeneficiariosListar extends ControllerValidation {
	tipo = null;
	headerMain = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	listRequests(tipo = '', pagina = 0) {
		const view = new BeneficiariosView({
			collection: {
				tipo: tipo,
				pagina: pagina,
			},
		});

		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		this.layout.getRegion('body').show(view);
	}
}
