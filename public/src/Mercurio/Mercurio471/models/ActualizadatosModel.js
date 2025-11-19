import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class ActualizadatosModel extends Backbone.Model {
    constructor(options = {}) {
        super(options);

        this.on('change:tipdoc', (model) => {
            let coddocrepleg = ActualizadatosModel.changeTipdoc(model.get('tipdoc'));
            model.set('coddocrepleg', coddocrepleg);
        });
    }

    get idAttribute() {
        return 'id';
    }

    get defaults() {
        return {
            id: null,
            codsuc: void 0,
            nit: void 0,
            razsoc: '',
            sigla: '',
            digver: void 0,
            calemp: '',
            cedrep: '',
            repleg: '',
            telefono: void 0,
            celular: void 0,
            email: '',
            tottra: void 0,
            valnom: void 0,
            tipemp: '',
            tipper: '',
            priape: '',
            prinom: '',
            segnom: '',
            segape: '',
            codact: void 0,
            coddocrepleg: void 0,
            coddoc: void 0,
            estado: void 0,
            tipdoc: void 0,
        };
    }

    validate(attr = {}) {
        return RulesValidator(ActualizadatosModel.Rules.rules, attr);
    }

    static Rules = {
        rules: {
            nit: { required: true, minlength: 6 },
            razsoc: { required: true, minlength: 5 },
            sigla: { required: false },
            digver: { required: false, minlength: 1 },
            calemp: { required: false, minlength: 1 },
            cedrep: { required: true, minlength: 6 },
            repleg: { required: true, minlength: 6 },
            telefono: { required: true, minlength: 7 },
            celular: { required: true, minlength: 10 },
            email: { required: true },
            tottra: { required: false },
            dirpri: { required: false },
            ciupri: { required: true },
            celpri: { required: false },
            emailpri: { required: false },
            tipemp: { required: false },
            tipper: { required: true },
            prinom: { required: true },
            priape: { required: true },
            codsuc: { required: true },
            coddoc: { required: true },
            coddocrepleg: { required: true },
            tipdoc: { required: true },
        },
        messages: {
            nit: { required: 'Se requiere del campo nit' },
            razsoc: { required: 'Se requiere del campo razón social' },
            sigla: { required: 'Se requiere del campo sigla' },
            digver: { required: 'Se requiere del campo digito verificación' },
            calemp: { required: 'Se requiere del campo calidad empresa' },
            cedrep: { required: 'Se requiere del campo cedula' },
            repleg: { required: 'Se requiere del campo nombre' },
            telefono: { required: 'Se requiere del campo télefono' },
            celular: { required: 'Se requiere del campo celular' },
            email: { required: 'Se requiere del campo email' },
            tottra: { required: 'Se requiere del campo total trabajadores' },
            dirpri: { required: 'Se requiere del campo dirección' },
            ciupri: { required: 'Se requiere del campo ciudad' },
            celpri: { required: 'Se requiere del campo celular' },
            emailpri: { required: 'Se requiere del campo email' },
            tipemp: { required: 'Se requiere del campo tipo empresa' },
            tipper: { required: 'Se requiere del campo tipo persona' },
            prinom: { required: 'Se requiere del campo primer nombre' },
            priape: { required: 'Se requiere del campo primer apellido' },
            fectra: { required: 'Se requiere del campo fecha' },
            direccion: { required: 'Se requiere del campo dirección' },
            codzon: { required: 'Se requiere de la zona laboral' },
            codact: { required: 'Se requiere de la actividad económica' },
            tipsoc: { required: 'Se requiere de tipo sociedad' },
            codciu: { required: 'Se requiere la ciudad' },
        },
    };

    static changeTipdoc(index) {
        let coddoc_repleg = {
            1: 'CC',
            10: 'TMF',
            11: 'CD',
            12: 'ISE',
            13: 'V',
            14: 'PT',
            2: 'TI',
            3: 'NI',
            4: 'CE',
            5: 'NU',
            6: 'PA',
            7: 'RC',
            8: 'PEP',
            9: 'CB',
        };
        return coddoc_repleg[index];
    }
}

export { ActualizadatosModel };
