import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { AportesView } from '@/Componentes/Views/AportesView';

export default class PensionadoAportes extends ControllerValidation {
	#tipo = null;
	#headerMain = null;
	#headerView = null;

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
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Pensionados',
				detalle: 'Aportes de pensionado con identificación de ' + model.cedtra,
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);

		model.option = {
			deshacer: false,
			aportes: false,
			volver: true,
			editar: false,
			notificar: false,
			info: false,
		};

		this.headerView = new HeaderInfoView({
			model: model,
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:notificar', this.__notificarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:info', this.__infoRequest);

		this.layout.getRegion('subheader').show(this.headerView);
	}
}
