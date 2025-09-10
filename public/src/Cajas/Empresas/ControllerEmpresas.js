import { $App } from '@/App';
import { ReportesController } from '@/Cajas/ReportesController';
import { Controller } from '@/Common/Controller';
import loading from '@/Componentes/Views/Loading';
import { EmpresaEditar } from './EmpresaEditar';
import { EmpresaInformation } from './EmpresaInformation';
import { EmpresasListas } from './EmpresasListas';

import AportesCollection from './collections/AportesCollection';
import EmpresaAportes from './EmpresaAportes';
import EmpresaDeshacer from './EmpresaDeshacer';
import EmpresaReaprobar from './EmpresaReaprobar';
import EmpresaModel from './models/EmpresaModel';

class ControllerEmpresas extends Controller {
    constructor(options = {}) {
        super(options);
    }

    listRequests(tipo = '', pagina = 0) {
        loading.hide();
        const app = this.startController(EmpresasListas);
        app.listRequests(tipo, pagina);
    }

    infoRequest(id = 0) {
        const app = this.startController(EmpresaInformation);
        $App.trigger('syncro', {
            url: 'infor',
            data: {
                id,
            },
            callback: (response) => {
                if (response) {
                    loading.hide();
                    app.infoRequest({
                        solicitud: new EmpresaModel(response.data),
                        empresa_sisuweb: response.empresa_sisuweb,
                        mercurio11: response.mercurio11,
                        consulta: response.consulta_empresa,
                        adjuntos: response.adjuntos,
                        seguimiento: response.seguimiento,
                        campos_disponibles: response.campos_disponibles,
                    });
                }
            },
        });
    }

    aportesRequest(id) {
        const app = this.startController(EmpresaAportes);
        const url = $App.kumbiaURL('aprobacionemp/aportes/' + id);
        $App.trigger('syncro', {
            url: url,
            data: {
                id: id,
            },
            callback: (response) => {
                loading.hide();
                if (response) {
                    const aportes = new AportesCollection(response.data);
                    const solicitud = new EmpresaModel(response.solicitud);
                    app.aportesRequest(solicitud, aportes);
                } else {
                    $App.trigger('alert:error', { message: 'No se pudo cargar la solicitud' });
                    $App.router.navigate('list', { trigger: true });
                }
            },
        });
    }

    editarRequest(id) {
        const app = this.startController(EmpresaEditar);
        $App.trigger('syncro', {
            url: 'infor',
            data: {
                id: id,
            },
            callback: (response) => {
                if (response) {
                    const solicitud = new EmpresaModel(response.data);
                    const collection = {
                        empresa_sisuweb: response.empresa_sisuweb,
                        mercurio11: response.mercurio11,
                        consulta: response.consulta_empresa,
                        campos_disponibles: response.campos_disponibles,
                    };
                    app.editarRequest(solicitud, collection);
                }
            },
        });
    }

    reportesRequest() {
        const app = this.startController(ReportesController);
        app.reportesRequest({
            request: 'Empresas',
            tipos: ['excel', 'pdf', 'csv'],
            estados: ['todo', 'A', 'R', 'P', 'X'],
        });
    }

    deshacerRequest(_id) {
        const app = this.startController(EmpresaDeshacer);
        app.deshacerRequest(_id);
    }

    reaprobarRequest(_id) {
        const app = this.startController(EmpresaReaprobar);
        app.reaprobarRequest(_id);
    }
}

export { ControllerEmpresas };
