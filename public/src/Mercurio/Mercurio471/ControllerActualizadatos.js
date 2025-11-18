import { $App } from '@/App';
import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import { ActualizadatosModel } from './models/ActualizadatosModel';
import { ActualizadatosView } from './views/ActualizadatosView';
import { FormActualizadatosView } from './views/FormActualizadatosView';

class ControllerActualizadatos extends ControllerRequest {
    constructor(options = {}) {
        super({
            ...options,
            EntityModel: ActualizadatosModel,
            TableView: ActualizadatosView,
            FormRequest: FormActualizadatosView,
            tipo: 'E',
            headerOptions: {
                estado: '',
                tipo: 'E',
                url_nueva: $App.url('nueva'),
                breadcrumb_menu: 'Crear solicitud',
                titulo: 'Actualiza datos empresa',
                url_masivo: null,
                isNew: null,
                create: 'enabled',
            },
        });

        if (this.services) _.extend(this, this.services);

        this.on('form:cancel', this.destroy);

        this.on('form:digit', this.afiService.digitVer);

        this.on('params', this.afiService.paramsServer);

        $App.Collections.formParams = null;
        $App.Collections.firmas = null;
    }

    __empresaSisu(transfer) {
        const { callback } = transfer;
        $App.trigger('syncro', {
            url: $App.url('empresa_sisu', window.ServerController ?? 'empresa'),
            data: {},
            callback: (response) => {
                if (response) {
                    return callback(response);
                }
                return callback(false);
            },
        });
    }
}

export { ControllerActualizadatos };
