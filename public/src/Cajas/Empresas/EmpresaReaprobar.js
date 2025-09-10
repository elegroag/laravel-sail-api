import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { $App } from '@/App';
import ReaprobarView from '@/Componentes/Views/ReaprobarView';

export default class EmpresaReaprobar extends ControllerValidation {
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	reaprobarRequest(id) {
		const view = new ReaprobarView({
			model: {
				tipo: 'E',
				id: id,
			},
		});

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Empresas',
				detalle: 'Reaprobar solicitud de empresa',
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: id,
				estado: 'A',
				option: {
					volver: true,
					deshacer: false,
					aportes: false,
					editar: false,
					info: true,
					notificar: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);
		this.layout.getRegion('subheader').show(this.headerView);

		this.listenTo(view, 'run:deshacer', this.__deshacerSolicitud);
		this.layout.getRegion('body').show(view);
	}
}
