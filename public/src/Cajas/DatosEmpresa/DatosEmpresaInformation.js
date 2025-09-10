import { ControllerValidation } from '@/Cajas/ControllerValidation';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { HeaderCajasView } from '@/Cajas/HeaderCajasView';
import EmpresaAprobarModel from '../Empresas/models/EmpresaAprobarModel';
import EmpresaModel from '../Empresas/models/EmpresaModel';
import DatosEmpresaInfoView from './views/DatosEmpresaInfoView';

export default class DatosEmpresaInformation extends ControllerValidation {
	solicitudModel = null;

	constructor(options = {}) {
		super(options);
		this.layout = new LayoutCajasView();
		this.region.show(this.layout);
	}

	infoRequest(response) {
		this.solicitudModel = new EmpresaModel(response.data);
		const entity = new EmpresaAprobarModel();
		entity.set('id', this.solicitudModel.get('id'));

		const view = new DatosEmpresaInfoView({
			model: entity,
			collection: {
				solicitud: this.solicitudModel,
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

		this.layout.getRegion('body').show(view);
		this.loadHeader()
	}

	loadHeader() {
		this.headerMain = new HeaderCajasView({
			model: {
				titulo: 'Aprobar datos empresas',
				detalle: 'Lista datos empresas ' + this.__estados[this.solicitudModel.get('estado')],
				info: false,
			},
		});
		this.layout.getRegion('header').show(this.headerMain);

		this.headerView = new HeaderInfoView({
			model: {
				id: this.solicitudModel.get('id'),
				estado: this.solicitudModel.get('estado'),
				option: {
					deshacer: true,
					aportes: true,
					volver: true,
					editar: false,
					notificar: false,
					info: false,
				}
			}
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		this.listenTo(this.headerView, 'load:aportes', this.__aportesEmpresa);
		this.layout.getRegion('subheader').show(this.headerView);
	}

	__aportesEmpresa(data) {
		this.App.router.navigate('aportes/' + data.id, { trigger: true });
	}

	__editarRequest(data) {
		this.App.router.navigate('edit/' + data.id, { trigger: true });
	}

	__volverLista() {
		this.App.router.navigate('list', { trigger: true, replace: true });
	}
}
