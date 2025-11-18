import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class BeneficiarioModel extends Backbone.Model {
    constructor(options) {
        super(options);
    }

    get idAttribute() {
        return 'id';
    }

    get defaults() {
        return {
            id: null,
            tipdoc: '',
            numdoc: 0,
            priape: '',
            prinom: '',
            fecnac: '',
            sexo: '',
            parent: '',
            huerfano: '',
            tiphij: '',
            nivedu: '',
            captra: '',
            tipdis: '',
            estado: null,
            cedtra: null,
            cedcon: null,
            biocedu: '',
            biotipdoc: '',
            bioprinom: '',
            biopriape: '',
            biocodciu: '',
            biodire: '',
            bioemail: '',
            biophone: '',
        };
    }

    validate(attr = {}) {
        return RulesValidator(BeneficiarioModel.Rules.rules, attr);
    }

    static Rules = {
        rules: {
            tipdoc: { required: true },
            numdoc: { required: true, minlength: 5, maxlength: 19 },
            cedtra: { required: true, number: true },
            priape: { required: true, maxlength: 34 },
            prinom: { required: true, maxlength: 34 },
            fecnac: { required: true },
            sexo: { required: true },
            parent: { required: true },
            huerfano: { required: true },
            tiphij: { required: true },
            nivedu: { required: true },
            captra: { required: true },
            tipdis: { required: true },
            biocedu: { required: true },
            biotipdoc: { required: true },
            bioprinom: { required: true },
            biopriape: { required: true },
            biocodciu: { required: false },
            biodire: { required: false },
            bioemail: { required: false },
            biophone: { required: false },
            tippag: { required: false },
            numcue: { required: false },
            tipcue: { required: false },
            codban: { required: false },
        },
        messages: {
            tipdoc: { required: 'Se requiere del campo tipo documento' },
            numdoc: { required: 'Se requiere del campo número documento' },
            cedtra: {
                required: 'Se requiere del campo cedula trabajador',
                number: 'Se requiere del campo cedula trabajador',
                minlength: window.$.validator.format('Por lo menos {0} caracteres son necesarios'),
                maxlength: window.$.validator.format('Lo maximo son {0} caracteres'),
            },
            priape: { required: 'Se requiere del campo primer apellido' },
            prinom: { required: 'Se requiere del campo primer nombre' },
            fecnac: { required: 'Se requiere del campo fecha nacimiento' },
            sexo: { required: 'Se requiere del campo sexo' },
            parent: { required: 'Se requiere del campo parentesco' },
            huerfano: { required: 'Se requiere del campo huerfano' },
            tiphij: { required: 'Se requiere del campo tipo hijo' },
            nivedu: { required: 'Se requiere del campo nivel educativo' },
            captra: { required: 'Se requiere del campo capacidad trabajar' },
            tipdis: { required: 'Se requiere del campo tipo discapacidad' },
            biocedu: { required: 'Se requiere del campo cedula' },
            biotipdoc: { required: 'Se requiere del campo tipo documento' },
            bioprinom: { required: 'Se requiere del campo primer nombre' },
            biopriape: { required: 'Se requiere del campo primer apellido' },
            biocodciu: { required: 'Se requiere del campo ciudad' },
            biodire: { required: 'Se requiere del campo dirección' },
            bioemail: { required: 'Se requiere del campo email' },
            biophone: { required: 'Se requiere del campo télefono' },
            tippag: { required: 'Se requiere del campo tipo pago' },
            numcue: { required: 'Se requiere del campo número cuenta' },
            tipcue: { required: 'Se requiere del campo tipo cuenta' },
            codban: { required: 'Se requiere del campo banco' },
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

    static changeRuleProperty(transfer = {}) {
        const { rule, prop, value } = transfer;
        BeneficiarioModel.Rules.rules[rule][prop] = value;
    }

    static changeRulesProperty(transfer = []) {
        _.each(transfer, (row) => BeneficiarioModel.changeRuleProperty(row));
    }
}

export { BeneficiarioModel };
