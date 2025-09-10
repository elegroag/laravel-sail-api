import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import EditarEmpresaView from './views/EditarEmpresaView';

class EmpresaEditar extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	editarRequest(solicitud, collection) {
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);

		const view = new EditarEmpresaView({
			model: solicitud,
			collection: collection,
		});

		this.loadHeaders(solicitud.toJSON());

		this.layout.getRegion('body').show(view);
	}

	loadHeaders(model) {
		this.headerView = new HeaderInfoView({
			model: {
				id: model.id,
				estado: model.estado,
				option: {
					deshacer: true,
					aportes: true,
					volver: true,
					editar: false,
					info: true,
					notificar: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);

		this.layout.getRegion('subheader').show(this.headerView);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar empresa',
				detalle: 'Editar solicitud de empresa ' + model.nit,
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);
	}
}

export { EmpresaEditar };
