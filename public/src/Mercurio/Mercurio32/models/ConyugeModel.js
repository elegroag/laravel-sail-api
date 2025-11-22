import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class ConyugeModel extends Backbone.Model {
    constructor(options) {
        super(options);
    }

    get idAttribute() {
        return 'id';
    }

    get defaults() {
        return {
            id: null,
            cedcon: 0,
            cedtra: 0,
            tipdoc: '',
            priape: '',
            prinom: '',
            fecnac: '',
            ciunac: '',
            sexo: '',
            estciv: '',
            comper: '',
            codzon: '',
            tipviv: '',
            telefono: 0,
            celular: 0,
            email: '',
            nivedu: '',
            fecing: '',
            codocu: '',
            salario: 0,
            fecsol: '',
            estado: void 0,
            numcue: '',
            codban: '',
            tippag: '',
            nit: '',
            zoneurbana: 'N',
        };
    }

    validate(attr = {}) {
        return RulesValidator(ConyugeModel.Rules.rules, attr);
    }

    static Rules = {
        rules: {
            cedcon: { required: true, rangelength: [5, 18] },
            cedtra: { required: true, number: true },
            tipdoc: { required: true, rangelength: [1, 3] },
            priape: { required: true },
            prinom: { required: true },
            fecnac: { required: true, date: true },
            ciunac: { required: true },
            sexo: { required: true },
            estciv: { required: true },
            comper: { required: true },
            codzon: { required: true },
            tipviv: { required: true },
            telefono: { required: true, number: true, rangelength: [7, 10] },
            celular: { required: true, number: true, rangelength: [7, 10] },
            email: { required: false },
            nivedu: { required: true },
            codocu: { required: true },
        },
        messages: {
            cedcon: { required: 'Se requiere del campo cedula conyuge' },
            cedtra: {
                required: 'Se requiere del campo cedula trabajador',
                number: 'Se requiere del campo cedula trabajador',
                minlength: window.$.validator.format('Por lo menos {0} caracteres son necesarios'),
                maxlength: window.$.validator.format('Lo maximo son {0} caracteres'),
            },
            tipdoc: { required: 'Se requiere del campo tipo documento' },
            priape: { required: 'Se requiere del campo primer apellido' },
            prinom: { required: 'Se requiere del campo primer nombre' },
            fecnac: { required: 'Se requiere del campo fecha nacimiento' },
            ciunac: { required: 'Se requiere del campo ciudad' },
            sexo: { required: 'Se requiere del campo sexo' },
            estciv: { required: 'Se requiere del campo estado civil' },
            comper: { required: 'Se requiere del campo compañero permanente' },
            codzon: { required: 'Se requiere del campo zona' },
            tipviv: { required: 'Se requiere del campo tipo vivienda' },
            telefono: { required: 'Se requiere del campo télefono' },
            celular: { required: 'Se requiere del campo celular' },
            email: { required: 'Se requiere del campo email' },
            nivedu: { required: 'Se requiere del campo nivel educativo' },
            codocu: { required: 'Se requiere del campo ocupación' },
        },
    };

    static changeTipdoc(index = 1) {
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

    static changeRuleProperty(transfer = {}) {
        const { rule, prop, value } = transfer;
        ConyugeModel.Rules.rules[rule][prop] = value;
    }

    static changeRulesProperty(transfer = []) {
        _.each(transfer, (row) => ConyugeModel.changeRuleProperty(row));
    }
}

export { ConyugeModel };
