import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import DeshacerView from '@/Componentes/Views/DeshacerView';
import { $App } from '@/App';

export default class IndependienteDeshacer extends ControllerValidation {
	headerMain = null;
	headerView = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	deshacerRequest(_id){
		const view = new DeshacerView({
			model: {
				tipo: 'I',
				id: _id
			}
		});

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Independientes',
				detalle: 'Deshacer aprobación de independiente',
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
