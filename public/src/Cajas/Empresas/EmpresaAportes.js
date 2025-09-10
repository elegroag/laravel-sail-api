import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { AportesView } from '@/Componentes/Views/AportesView';

export default class EmpresaAportes extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	aportesRequest(solicitud, aportes) {
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);

		this.loadHeaders(solicitud.toJSON());

		const view = new AportesView({
			model: solicitud,
			collection: aportes,
		});
		this.layout.getRegion('body').show(view);
	}

	loadHeaders(model) {
		const headerMain = new HeaderCajasView({
			model: {
				titulo: 'Empresas',
				detalle: 'Lista de aportes empresa ' + model.nit,
				info: false,
			},
		});

		this.layout.getRegion('header').show(headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				estado: model.estado,
				id: model.id,
				option: {
					deshacer: false,
					aportes: false,
					volver: true,
					editar: false,
					info: false,
					notificar: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);

		this.layout.getRegion('subheader').show(this.headerView);
	}
}
