import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { $App } from '@/App';
import ReaprobarView from '@/Componentes/Views/ReaprobarView';

export default class FacultativoReaprobar extends ControllerValidation {

	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	reaprobarRequest(_id){
		const view = new ReaprobarView({
			model: {
				tipo: 'T',
				id: _id
			}
		});

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Independiente',
				detalle: 'Reaprobar solicitud de facultativo',
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: _id,
				estado: 'A',
				option: {
					volver: true,
					deshacer: false,
					aportes: false,
					editar: false,
					info: false,
					notificar: false,
				}
			}
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.layout.getRegion('subheader').show(this.headerView);

		this.listenTo(view, 'run:deshacer', this.__deshacerSolicitud);
		this.layout.getRegion('body').show(view);
	}
}
