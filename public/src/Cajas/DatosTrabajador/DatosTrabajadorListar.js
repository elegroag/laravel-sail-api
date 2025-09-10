import { $App } from "@/App";
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import DatosTrabajadoresView from './views/DatosTrabajadoresView';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';

export default class DatosTrabajadorListar extends ControllerValidation {
	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	listRequests(tipo = '', pagina = 0) {
		const view = new DatosTrabajadoresView({
			collection: {
				tipo: tipo,
				pagina: pagina,
			},
		});

		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		$App.layout.getRegion('body').show(view);
	}
}
