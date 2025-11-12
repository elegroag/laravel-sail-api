import { ModelView } from '@/Common/ModelView';
import componente_address from '@/Componentes/Views/Templates/componente_address.hbs?raw';
import componente_date from '@/Componentes/Views/Templates/componente_date.hbs?raw';
import componente_dialog from '@/Componentes/Views/Templates/componente_dialog.hbs?raw';
import componente_input from '@/Componentes/Views/Templates/componente_input.hbs?raw';
import componente_radio from '@/Componentes/Views/Templates/componente_radio.hbs?raw';
import componente_select from '@/Componentes/Views/Templates/componente_select.hbs?raw';
import componente_textarea from '@/Componentes/Views/Templates/componente_textarea.hbs?raw';
import Choices from 'choices.js';

class SelectComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_select);
    }
}

class RadioComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_radio);
    }

    get className() {
        return 'form-group';
    }
}

class DateComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_date);
    }

    get className() {
        return 'form-group-date';
    }
}

class TextComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_textarea);
    }

    get className() {
        return 'form-group';
    }
}

class DialogComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_dialog);
    }

    get className() {
        return 'form-group';
    }
}

class InputComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_input);
    }
}

class OpenAddress extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.Modal = null;
    }

    initialize() {
        this.Modal = new bootstrap.Modal(document.getElementById('modal_generic'), {
            keyboard: true,
            backdrop: 'static',
        });
    }

    render() {
        const template = _.template(componente_address);
        this.$el.html(
            template({
                adress: this.collection,
            }),
        );
        $('#size_modal_generic').addClass('modal-lg');
        this.Modal.show();
        document.getElementById('modal_generic').addEventListener('hidden.bs.modal', (e) => {
            if ($('.modal:visible').length == 0) this.remove();
        });
        return this;
    }

    get events() {
        return {
            'blur [data-toggle="valida_caracteres"]': 'validaCaracteres',
            'click #button_address_modal': 'addressModal',
            'change #address_zona': 'addressZona',
            'click #address_one': 'addressOne',
            'click [data-dismiss="modal"]': 'closeModal',
        };
    }

    closeModal(e) {
        e.preventDefault();
        this.Modal.hide();
    }

    validaCaracteres(e) {
        let target = $(e.currentTarget);
        if (/[^a-zA-Z\ 0-9]/g.test(target.val())) {
            target.val('');
        }
    }

    addressModal(event) {
        event.preventDefault();
        let barrio = '';
        let address;

        if (this.$el.find('#address_five').val() !== '') {
            barrio = ' BRR ' + $('#address_five').val();
        }
        if (this.$el.find('#address_one').val() == null && $('#address_two').val() == '') {
            address = 'BRR';
        } else {
            address =
                this.$el.find('#address_one').val() +
                ' ' +
                this.$el.find('#address_two').val() +
                ' ' +
                this.$el.find('#address_four').val() +
                ' ' +
                barrio;
        }
        let target = document.getElementById(this.model.name);
        target.value = address;
        this.Modal.hide();
        if (address) {
            target.classList.add('is-valid');
            target.classList.remove('is-invalid');
            document.getElementById(this.model.name + '-error').textContent = '';
        }
    }

    addressZona(event) {
        event.preventDefault();
        const valor = $(event.currentTarget).val();
        let lista;
        if (valor === 'R') {
            lista = _.filter(this.collection, (row) => {
                return row.tipo_rural === 'S' || row.tipo_rural === 'V';
            });
            this.$el.find('#address_barrio').fadeOut();
            this.$el.find('#show_address_four').fadeOut();
            this.$el.find('#address_nombre_optional').text('Nombre ubicación');
            this.$el.find('#show_address_two').attr('class', 'col-md-4');
            this.$el.find('#address_one').removeAttr('disabled');

            new Choices(this.$el.find('#address_one')[0]);
        } else if (valor === 'U') {
            lista = _.filter(this.collection, (row) => {
                return row.tipo_rural === 'N';
            });
            this.$el.find('#show_address_four').fadeIn();
            this.$el.find('#address_barrio').fadeIn();
            this.$el.find('#address_nombre_optional').text('Número ');
            this.$el.find('#show_address_two').attr('class', 'col-md-2');
            this.$el.find('#address_one').removeAttr('disabled');

            new Choices(this.$el.find('#address_one')[0]);
        } else {
            lista = [];
            $('#address_one').attr('disabled', 'true');
        }

        let html = '';
        const template = _.template(`<option value="<%=estado%>"><%=detalle%></option>`);
        _.each(lista, (adres) => {
            html += template(adres);
        });
        this.$el.find('#address_one').html(html);
    }

    addressOne(event) {
        event.preventDefault();
        if (this.$el.find('#address_zona').val() == '') {
            this.$el.find('#address_zona').focus();
            this.$el.find('#address_one').val('');
        }
    }
}

export { DateComponent, DialogComponent, InputComponent, OpenAddress, RadioComponent, SelectComponent, TextComponent };
