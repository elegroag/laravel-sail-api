import { ModelView } from '@/Common/ModelView';
import componente_address from '@/Componentes/Views/Templates/componente_address.hbs?raw';
import componente_date from '@/Componentes/Views/Templates/componente_date.hbs?raw';
import componente_dialog from '@/Componentes/Views/Templates/componente_dialog.hbs?raw';
import componente_input from '@/Componentes/Views/Templates/componente_input.hbs?raw';
import componente_radio from '@/Componentes/Views/Templates/componente_radio.hbs?raw';
import componente_select from '@/Componentes/Views/Templates/componente_select.hbs?raw';
import componente_textarea from '@/Componentes/Views/Templates/componente_textarea.hbs?raw';

class SelectComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_select);
    }
}

class AddressComponent extends ModelView {
    constructor(options = {}) {
        super(options);
        this.template = _.template(componente_address);
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

export { AddressComponent, DateComponent, DialogComponent, InputComponent, RadioComponent, SelectComponent, TextComponent };
