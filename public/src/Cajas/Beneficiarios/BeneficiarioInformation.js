import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import { $App } from '@/App';
import { BeneficiarioInfoView } from './views/BeneficiarioInfoView';

export default class BeneficiarioInformation extends ControllerValidation {
	tipo = null;
	headerMain = null;
	headerView = null;
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
		this.titulo = 'Aprobar beneficiario';
		this.titulo_detalle = 'Lista beneficiarios';

		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
		$App.layout = this.layout;
	}

	infoRequest(solicitud, entity, collection) {

		this.solicitudModel = solicitud;

		const view = new BeneficiarioInfoView({
			model: entity,
			collection: {
				solicitud: solicitud,
				empresa_sisuweb: collection.empresa_sisuweb,
				beneficiario_sisu: collection.beneficiario_sisu,
				mercurio11: collection.mercurio11,
				consulta: collection.consulta,
				adjuntos: collection.adjuntos,
				seguimiento: collection.seguimiento,
				campos_disponibles: collection.campos_disponibles,
				empresa_sisu: collection.empresa_sisu,
				componente_codsuc: collection.componente_codsuc,
				componente_codlis: collection.componente_codlis,
			},
		});

		this.listenTo(view, 'load:aprobar', this.__aprobarSolicitud);
		this.listenTo(view, 'load:devolver', this.__devolverSolicitud);
		this.listenTo(view, 'load:rechazar', this.__rechazaSolicitud);

		this.layout.getRegion('body').show(view);

		this.loadHeaders(solicitud.toJSON());
	}

	loadHeaders(model) {
		model.option = {
			deshacer: model.estado == 'A'? true: false,
			aportes:  false,
			volver: true,
			editar:  model.estado == 'A'? false: true,
			info: false,
			notificar: false,
		};

		this.headerView = new HeaderInfoView({
			model: model,
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesInformation);
		this.listenTo(this.headerView, 'load:reaprobar', this.reaprobarSolicitud);
		this.listenTo(this.headerView, 'load:deshacer', this.deshacerSolicitud);

		this.layout.getRegion('subheader').show(this.headerView);

		const tipo_detalle = this.__estados[model.estado];
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: this.titulo,
				detalle: this.titulo_detalle + ' - ' + tipo_detalle,
				info: false,
			},
		});

		this.layout.getRegion('header').show(this.headerMain);
	}

	deshacerSolicitud(){
		const id = this.solicitudModel.get('id');
		this.App.router.navigate('deshacer/'+id, { trigger: true, replace: true });
	}

	reaprobarSolicitud() {
		const id = this.solicitudModel.get('id');
		this.App.router.navigate('reaprobar/'+id, { trigger: true, replace: true });
	}
}
