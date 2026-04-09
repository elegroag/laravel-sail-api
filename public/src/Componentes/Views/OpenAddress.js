import modal_address from '@/Componentes/Views/Templates/modal-address.hbs?raw';
import Choices from 'choices.js';

class OpenAddress extends Backbone.View {
    static Modal = null;

    constructor(options = {}) {
        super(options);
    }

    initialize() {
        OpenAddress.Modal = new bootstrap.Modal(document.getElementById('modal_generic'), {
            keyboard: true,
            backdrop: 'static',
        });
    }

    render() {
        const template = _.template(modal_address);
        this.$el.html(
            template({
                adress: this.collection,
            }),
        );
        $('#size_modal_generic').addClass('modal-lg');
        OpenAddress.Modal.show();
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
        OpenAddress.Modal.hide();
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
        const $el = this.$el;
        const target = document.getElementById(this.model.name);

        if ($el.find('#address_five').val() !== '') {
            barrio = ' BRR ' + $el.find('#address_five').val();
        }
        if ($el.find('#address_one').val() == null && $el.find('#address_two').val() == '') {
            address = 'BRR';
        } else {
            address = $el.find('#address_one').val() + ' ' + $el.find('#address_two').val() + ' ' + $el.find('#address_four').val() + ' ' + barrio;
        }

        target.value = address;
        OpenAddress.Modal.hide();
        if (address) {
            target.classList.add('is-valid');
            target.classList.remove('is-invalid');
            $el.find('#' + this.model.name + '-error').text('');
        }
    }

    addressZona(event) {
        event.preventDefault();
        const valor = $(event.currentTarget).val();
        let lista;
        const $el = this.$el;
        if (valor === 'R') {
            lista = _.filter(this.collection, (row) => {
                return row.tipo_rural === 'S' || row.tipo_rural === 'V';
            });
            $el.find('#address_barrio').fadeOut();
            $el.find('#show_address_four').fadeOut();
            $el.find('#address_nombre_optional').text('Nombre ubicación');
            $el.find('#show_address_two').attr('class', 'col-md-4');
            $el.find('#address_one').removeAttr('disabled');
        } else if (valor === 'U') {
            lista = _.filter(this.collection, (row) => {
                return row.tipo_rural === 'N';
            });
            $el.find('#show_address_four').fadeIn();
            $el.find('#address_barrio').fadeIn();
            $el.find('#address_nombre_optional').text('Número ');
            $el.find('#show_address_two').attr('class', 'col-md-2');
            $el.find('#address_one').removeAttr('disabled');
        } else {
            lista = [];
            $el.find('#address_one').attr('disabled', 'true');
        }

        let html = '';
        const template = _.template(`<option value="<%=estado%>"><%=detalle%></option>`);
        $.each(lista, (index, adres) => {
            html += template(adres);
        });
        $el.find('#address_one').html(html);

        new Choices($el.find('#address_one')[0], {
            silent: true,
            itemSelectText: null,
        });
    }

    addressOne(event) {
        event.preventDefault();
        if (this.$el.find('#address_zona').val() == '') {
            this.$el.find('#address_zona').focus();
            this.$el.find('#address_one').val('');
        }
    }
}

export default OpenAddress;
