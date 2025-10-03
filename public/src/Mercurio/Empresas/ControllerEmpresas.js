import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import { EmpresaModel } from './models/EmpresaModel';
import { EmpresasView } from './views/EmpresasView';
import { FormEmpresaView } from './views/FormEmpresaView';

class ControllerEmpresas extends ControllerRequest {
    constructor(options) {
        super({
            ...options,
            EntityModel: EmpresaModel,
            TableView: EmpresasView,
            FormRequest: FormEmpresaView,
            tipo: 'E',
            headerOptions: {
                estado: '',
                tipo: 'E',
                url_nueva: options.App.url('nueva'),
                breadcrumb_menu: 'Crear solicitud',
                titulo: 'Afiliación empresa',
                url_masivo: null,
                isNew: null,
                create: 'enabled',
            },
        });

        if (this.services) _.extend(this, this.services);

        this.on('form:cancel', this.destroy);
        this.on('params', this.__paramsServer);
        options.App.Collections.formParams = null;
        options.App.Collections.firmas = null;
    }

    __paramsServer({ callback = undefined, silent = false }) {
        this.App.trigger('syncro', {
            url: this.App.url('empresas/params'),
            silent,
            callback: (response) => {
                if (response && response.success === true) {
                    if (_.isNull(this.App.Collections.formParams)) this.App.Collections.formParams = [];
                    _.extend(this.App.Collections.formParams, response.data);
                    return callback !== false ? callback(response.msj) : null;
                }
                return callback !== false ? callback(false) : null;
            },
        });
    }
}

export { ControllerEmpresas };
