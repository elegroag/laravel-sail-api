import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import IndependienteEditView from './views/IndependienteEditView';

export default class IndependienteEditar extends ControllerValidation {
	constructor(options = {}) {
		super(options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.App.layout = this.layout;
	}

	editarRequest(collection = {}) {
		this.solicitudModel = collection.solicitud;

		const view = new IndependienteEditView({
			model: collection.solicitud,
			collection: collection,
		});
		this.layout.getRegion('body').show(view);
		this.loadHeaders();
	}

	loadHeaders() {
		const headerMain = new HeaderCajasView({
			model: {
				titulo: 'Independientes',
				detalle: 'Editar independiente ' + this.solicitudModel.get('nit'),
				info: false,
			},
		});

		this.layout.getRegion('header').show(headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: this.solicitudModel.get('id'),
				estado: this.solicitudModel.get('estado'),
				option: {
					deshacer: false,
					aportes: false,
					volver: true,
					editar: false,
					info: true,
					notificar: true,
				},
			},
		});
		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.layout.getRegion('subheader').show(this.headerView);
	}
}
