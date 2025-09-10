import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { $App } from '@/App';
import TrabajadorTrayectoriaView from './views/TrabajadorTrayectoriaView';
import LayoutTrabajador from './views/LayoutTrabajador';
import TrabajadorModel from './models/TrabajadorModel';
import TrabajadorSalarioView from './views/TrabajadorSalarioView';
import TrabajadorSisuView from './views/TrabajadorSisuView';

export default class TrabajadorTrayectoria extends ControllerValidation {
	headerMain = null;
	headerView = null;
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	trayectoriaRequest(response) {
		this.solicitudModel = new TrabajadorModel(response.solicitud);

		const layoutTra = new LayoutTrabajador();
		this.layout.getRegion('body').show(layoutTra);
		this.loadHeaders();

		const view = new TrabajadorTrayectoriaView({
			model: {
				trayectorias: response.trayectorias,
			},
		});

		layoutTra.getRegion('trayectorias').show(view);

		const viewSalario = new TrabajadorSalarioView({
			model: {
				salarios: response.salarios,
			},
		});
		layoutTra.getRegion('salarios').show(viewSalario);

		const viewTrabajador = new TrabajadorSisuView({
			model: response.trabajador,
		});
		layoutTra.getRegion('trabajador').show(viewTrabajador);
	}

	loadHeaders() {
		const estado = this.solicitudModel.get('estado');
		const id = this.solicitudModel.get('id');

		this.headerView = new HeaderInfoView({
			model: {
				estado: estado,
				id: id,
				option: {
					volver: true,
					info: true,
					deshacer: false,
					aportes: false,
					editar: false,
					notificar: false,
					trayectoria: false,
				},
			},
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);
		this.layout.getRegion('subheader').show(this.headerView);

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Trayectoria del trabajador',
				detalle: 'Historial Laboral',
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);
	}
}
