'use strict';

const componenetDefaultAttribute = {
    name: null,
    type: 'text',
    info: void 0,
    label: void 0,
    placeholder: void 0,
    disabled: false,
    readonly: false,
    order: 1,
    form: 'inputText',
    grupo: 1,
    target: -1,
    valor: '',
    event: void 0,
    data: void 0,
    datemax: '',
    searchType: false,
    search: '',
    search_type: 'local',
    className: '',
    form_type: 'text',
};

class ComponentModel extends Backbone.Model {
    constructor(options = {}) {
        super(options);
    }

    get idAttribute() {
        return 'name';
    }

    get defaults() {
        return {
            ...componenetDefaultAttribute,
        };
    }
}

class ComponentValidModel extends ComponentModel {
    constructor(options = {}) {
        super(options);
    }

    get idAttribute() {
        return 'name';
    }

    get defaults() {
        return {
            ...componenetDefaultAttribute,
            pattern: '', //tipo validacion
            dvalor: '', //valor por defecto
            longitud: '', //valor de longitud del campo
            rango: '', //valor de rango para datos numericos
            size: 42,
            detalle: '',
        };
    }
}

export { ComponentModel, ComponentValidModel, componenetDefaultAttribute };
