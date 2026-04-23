import { Controller } from '@/Common/Controller';
import { BeneficiarioAprobarModel } from './models/BeneficiarioAprobarModel';
import { BeneficiarioModel } from './models/BeneficiarioModel';

import BeneficiarioDeshacer from './BeneficiarioDeshacer';
import BeneficiarioInformation from './BeneficiarioInformation';
import BeneficiarioReaprobar from './BeneficiarioReaprobar';
import BeneficiariosListar from './BeneficiariosListar';

class ControllerBeneficiarios extends Controller {
    constructor(options = {}) {
        super(options);
    }

    listRequests(tipo = '', pagina = 0) {
        const app = this.startController(BeneficiariosListar);
        app.listRequests(tipo, pagina);
    }

    infoRequest(_id) {
        const app = this.startController(BeneficiarioInformation);
        this.App.trigger('syncro', {
            url: 'infor',
            data: {
                id: _id,
            },
            callback: (response) => {
                if (response) {
                    const solicitud = new BeneficiarioModel(response.data);
                    const entity = new BeneficiarioAprobarModel({
                        id: solicitud.get('id'),
                        fecpre: solicitud.get('fecsol'),
                        numhij: '1',
                    });
                    app.infoRequest(solicitud, entity, response);
                }
            },
        });
    }

    deshacerRequest(_id) {
        const app = this.startController(BeneficiarioDeshacer);
        app.deshacerRequest(_id);
    }

    reaprobarRequest(_id) {
        const app = this.startController(BeneficiarioReaprobar);
        app.reaprobarRequest(_id);
    }
}

export { ControllerBeneficiarios };
