import { ReportesView } from '@/Componentes/Views/ReportesView';
import { Region } from '@/Common/Region';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';

class ReportesController {
	constructor(options = {}) {
		_.extend(this, options);
		_.extend(this, Backbone.Events);
	}

	reportesRequest(options = {}) {
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		this.view = new ReportesView({
			model: options,
		});
		this.layout.getRegion('body').show(this.view);
		this.loadHeaders(options);
	}

	loadHeaders(options = { request: '' }) {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Reporte',
				detalle: 'Reporte ' + options.request,
				info: true,
			},
		});

		this.listenTo(this.headerMain, 'show:filtro', this.__showFiltro);

		this.layout.getRegion('header').show(this.headerMain);
	}

	destroy() {
		this.stopListening();
		if (this.region && this.region instanceof Region) this.region.remove();
	}
}

export { ReportesController };
