import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import { PensionadoModel } from './models/PensionadoModel';
import { FormPensionadoView } from './views/FormPensionadoView';
import { PensionadosView } from './views/PensionadosView';

class ControllerPensionados extends ControllerRequest {
    constructor(options) {
        super({
            ...options,
            EntityModel: PensionadoModel,
            TableView: PensionadosView,
            FormRequest: FormPensionadoView,
            tipo: 'O',
            headerOptions: {
                estado: '',
                tipo: 'O',
                url_nueva: options.App.url('nueva'),
                breadcrumb_menu: 'Crear solicitud',
                titulo: 'Afiliación pensionado',
                url_masivo: null,
                isNew: null,
                create: 'enabled',
            },
        });

        if (this.services) _.extend(this, this.services);
        this.on('form:cancel', this.destroy);
        this.on('form:find', this.afiService.validePk);
        this.on('form:digit', this.afiService.digitVer);
        this.once('params', this.afiService.paramsServer);

        options.App.Collections.formParams = null;
        options.App.Collections.firmas = null;
    }
}

export { ControllerPensionados };
