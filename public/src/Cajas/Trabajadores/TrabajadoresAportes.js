import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { AportesView } from '@/Componentes/Views/AportesView';

export default class TrabajadoresAportes extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
	}

	aportesRequest(solicitud, aportes) {
		this.solicitudModel = solicitud;
		this.loadHeaders();

		const view = new AportesView({
			model: solicitud,
			collection: aportes,
		});
		this.layout.getRegion('body').show(view);
	}

	loadHeaders() {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Trabajadores',
				detalle: 'Lista de aportes trabajador ' + this.solicitudModel.get('nit'),
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				estado: this.solicitudModel.get('estado'),
				id: this.solicitudModel.get('id'),
				option: {
					deshacer: false,
					aportes: false,
					volver: true,
					editar: false,
					notificar: false,
					info: true,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:notificar', this.__notificarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);

		this.layout.getRegion('subheader').show(this.headerView);
	}
}
