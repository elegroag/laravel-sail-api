import { $App } from '@/App';
import { ControllerRequest } from '@/Mercurio/ControllerRequest';
import { FacultativoModel } from './models/FacultativoModel';
import { FacultativosView } from './views/FacultativosViews';
import { FormFacultativoView } from './views/FormFacultativoView';

class ControllerFacultativos extends ControllerRequest {
    constructor(options = {}) {
        super({
            ...options,
            EntityModel: FacultativoModel,
            TableView: FacultativosView,
            FormRequest: FormFacultativoView,
            tipo: 'F',
            headerOptions: {
                estado: '',
                tipo: 'F',
                url_nueva: $App.url('nueva'),
                breadcrumb_menu: 'Crear solicitud',
                titulo: 'Afiliación facultativo',
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

        $App.Collections.formParams = null;
        $App.Collections.firmas = null;
    }
}

export { ControllerFacultativos };
