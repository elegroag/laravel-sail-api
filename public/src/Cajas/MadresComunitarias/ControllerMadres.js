import { $App } from '@/App';
import { ControllerValidation } from '@/Cajas/ControllerValidation';
import IndependienteAprobarModel from '../Independientes/models/IndependienteAprobarModel';
import IndependienteModel from '../Independientes/models/IndependienteModel';
import { MadreInfoView } from './MadreInfoView';
import { MadresView } from './MadresView';

class ControllerMadres extends ControllerValidation {
	constructor(options = {}) {
		super(options);
	}

	listRequests(tipo = '', pagina = 0) {
		this.initialize();

		const view = new MadresView({
			collection: {
				tipo: tipo,
				pagina: pagina,
			},
		});
		this.listenTo(view, 'load:table', this.__aplicarFiltro);
		this.listenTo(view, 'load:pagina', this.__buscarPagina);
		this.listenTo(view, 'change:pagina', this.__cambiarPagina);
		$App.layout.getRegion('body').show(view);
	}

	infoRequest(_id) {
		this.initialize();
		$App.trigger('syncro', {
			url: 'infor',
			data: {
				id: _id,
			},
			callback: (response) => {
				if (response) {
					const solicitud = new IndependienteModel(response.data);
					const entity = new IndependienteAprobarModel();
					entity.set('id', solicitud.get('id'));

					const view = new MadreInfoView({
						model: entity,
						collection: {
							solicitud: solicitud,
							empresa_sisuweb: response.empresa_sisuweb,
							mercurio11: response.mercurio11,
							consulta: response.consulta_empresa,
							adjuntos: response.adjuntos,
							seguimiento: response.seguimiento,
							campos_disponibles: response.campos_disponibles,
						},
					});

					this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
					this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
					this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

					$App.layout.getRegion('body').show(view);
				}
			},
		});
	}

	aportesRequest(_id) {}

	editarRequest(_id) {}
}

export { ControllerMadres };
