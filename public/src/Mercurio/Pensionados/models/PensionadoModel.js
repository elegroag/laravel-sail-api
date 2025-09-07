import { RulesValidator } from '@/Componentes/Services/RulesValidator';

class PensionadoModel extends Backbone.Model {
    constructor(options) {
        super(options);
        this.on('change:tipdoc', (model) => {
            let coddocrepleg = PensionadoModel.changeTipdoc(model.get('tipdoc'));
            model.set('coddocrepleg', coddocrepleg);
        });
    }

    get idAttribute() {
        return 'id';
    }

    get defaults() {
        return {
            id: null,
            calemp: '',
            telefono: void 0,
            celular: void 0,
            email: '',
            fecini: void 0,
            priape: '',
            prinom: '',
            tipdoc: void 0,
            codact: void 0,
            coddocrepleg: void 0,
            cedtra: 0,
            segape: '',
            segnom: '',
            fecnac: '',
            ciunac: '',
            sexo: '',
            orisex: '',
            estciv: '',
            cabhog: '',
            codciu: 0,
            codzon: 0,
            direccion: '',
            barrio: '',
            fecsol: '',
            salario: 0,
            captra: '',
            tipdis: '',
            nivedu: '',
            rural: '',
            vivienda: '',
            tipafi: '',
            cargo: '',
            autoriza: '',
            facvul: '',
            peretn: '',
            tippag: '',
            numcue: '',
            codcaj: '',
            estado: '',
            dirlab: '',
        };
    }

    validate(attr = {}) {
        return RulesValidator(PensionadoModel.Rules.rules, attr);
    }

    static Rules = {
        rules: {
            calemp: { required: true, minlength: 1 },
            cedtra: { required: true, minlength: 6 },
            telefono: { required: true, minlength: 7, number: true },
            celular: { required: true, minlength: 10, number: true },
            email: { required: true },
            fecini: { required: true, minlength: 10, date: true },
            priape: { required: true },
            prinom: { required: true },
            tipdoc: { required: true, number: true },
            codact: { required: true },
            ciunac: { required: true },
            sexo: { required: true },
            orisex: { required: true },
            estciv: { required: true },
            cabhog: { required: true },
            codciu: { required: true },
            codzon: { required: true },
            direccion: { required: true },
            barrio: { required: false },
            fecsol: { required: true, date: true },
            salario: { required: true, number: true },
            captra: { required: true },
            tipdis: { required: true },
            nivedu: { required: true },
            rural: { required: true },
            vivienda: { required: true },
            tipafi: { required: true },
            cargo: { required: true },
            autoriza: { required: true },
            facvul: { required: true },
            peretn: { required: true },
            tippag: { required: true },
            numcue: { required: false },
            codcaj: { required: true },
            codban: { required: true },
            tipcue: { required: true },
            dirlab: { required: true },
            fecnac: { required: true, date: true },
        },
        messages: {
            calemp: { required: 'Se requiere del campo calidad empresa' },
            cedtra: { required: 'Se requiere del campo cedula' },
            telefono: { required: 'Se requiere del campo télefono' },
            celular: { required: 'Se requiere del campo celular' },
            email: { required: 'Se requiere del campo email' },
            fecini: { required: 'Se requiere del campo fecha inicio' },
            priape: { required: 'Se requiere del campo primer apellido' },
            prinom: { required: 'Se requiere del campo primer nombre' },
            tipdoc: { required: 'Se requiere del campo tipo documento' },
            codact: { required: 'Se requiere del campo codigo actividad' },
            ciunac: { required: 'Se requiere del campo ciudad' },
            sexo: { required: 'Se requiere del campo sexo' },
            orisex: { required: 'Se requiere del campo orientación sexual' },
            estciv: { required: 'Se requiere del campo estado civil' },
            cabhog: { required: 'Se requiere del campo cabeza de hogar' },
            codciu: { required: 'Se requiere del campo ciudad' },
            codzon: { required: 'Se requiere del campo zona' },
            direccion: { required: 'Se requiere del campo dirección' },
            barrio: { required: 'Se requiere del campo barrio' },
            fecsol: { required: 'Se requiere del campo fecha solicitud' },
            salario: { required: 'Se requiere del campo salario' },
            captra: { required: 'Se requiere del campo capacidad trabajar' },
            tipdis: { required: 'Se requiere del campo tipo discapacidad' },
            nivedu: { required: 'Se requiere del campo nivel educativo' },
            rural: { required: 'Se requiere del campo rural' },
            vivienda: { required: 'Se requiere del campo vivienda' },
            tipafi: { required: 'Se requiere del campo tipo afiliado' },
            cargo: { required: 'Se requiere del campo cargo' },
            autoriza: { required: 'Se requiere del campo autoriza' },
            facvul: { required: 'Se requiere del campo factor vulnerabilidad' },
            peretn: { required: 'Se requiere del campo pertenencia étnica' },
            tippag: { required: 'Se requiere del campo tipo pagago' },
            numcue: { required: 'Se requiere del campo número cuenta' },
            codcaj: { required: 'Se requiere del campo caja' },
            codban: { required: 'Se requiere del campo banco' },
            tipcue: { required: 'Se requiere del campo tipo cuenta' },
            dirlab: { required: 'Se requiere del campo dirección laboral' },
            fecnac: { required: 'Se requiere del campo fecha nacimiento' },
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
        PensionadoModel.Rules.rules[rule][prop] = value;
    }

    static changeRulesProperty(transfer = []) {
        _.each(transfer, (row) => PensionadoModel.changeRuleProperty(row));
    }
}

export { PensionadoModel };
