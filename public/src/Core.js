const urlError = () => 'Url error is undefined!';

const emulateDefault = () => (Backbone.emulateHTTP = true);

const methodMap = {
    create: 'POST',
    update: 'PUT',
    fetch: 'GET',
    destroy: 'DELETE',
};

Backbone.sync = (method, model, options) => {
    const type = methodMap[method];

    const params = _.extend(
        {
            type: type,
            dataType: 'json',
            processData: false,
        },
        options,
    );

    if (!params.url) {
        params.url = model.url || urlError();
    }

    if (!params.data && model && (method == 'create' || method == 'update')) {
        params.contentType = 'application/json';
        params.data = JSON.stringify(model.toJSON());
    }

    if (!Backbone.emulateJSON && !Backbone.emulateHTTP) emulateDefault();

    if (Backbone.emulateJSON) {
        params.contentType = 'application/x-www-form-urlencoded';
        params.processData = true;
        params.data = params.data ? { model: params.data } : {};
        params.beforeSend = function (xhr) {
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        };
    }

    if (Backbone.emulateHTTP) {
        if (type === 'PUT' || type === 'DELETE') {
            if (Backbone.emulateJSON) params.data._method = type;
            params.type = 'POST';
            params.beforeSend = (xhr) => {
                xhr.setRequestHeader('X-HTTP-Method-Override', type);
            };
        }
    }

    return $.ajax(params);
};

const capitalize = (_string) => {
    if (typeof _string !== 'string') return '';
    const exp = _string.toLowerCase().split(' ');
    if (exp.length == 1) {
        _string = exp[0].charAt(0).toUpperCase() + exp[0].slice(1);
    }
    if (exp.length > 1) {
        const parts = new Array();
        _.each(exp, (parte) => {
            parts.push(parte.charAt(0).toUpperCase() + parte.slice(1));
        });
        _string = parts.join(' ');
    }
    return _string;
};

const is_email = (value) => {
    const pattern = /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+\.([a-zA-Z])+([a-zA-Z])+/;
    return pattern.test(value);
};

const is_numeric = (value) => {
    const numerico = /^([0-9]+){0,20}$/;
    return numerico.test(value);
};

const is_date = (value) => {
    const regla = /^([2]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/;
    return regla.test(value);
};

const is_date_general = (value) => {
    const regla = /^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/;
    return regla.test(value);
};

const Testeo = (() => {
    const showError = (target = '', message = '', out = true) => {
        const el = $(`[group-for='${target}']`);
        el.find('.form-control').addClass('invalid');

        if (out) {
            if (el.find('.validation-error')) el.find('.validation-error').remove();
            const toggle = document.createElement('div');
            const sel = $(toggle);
            sel.attr('class', 'validation-error');
            sel.html(`<span>${message}</span>`);
            el.append(sel);
            setTimeout(() => sel.remove(), 6000);
            el.find('.form-control').on('focus', () => {
                sel.remove();
                el.find('.form-control').removeClass('invalid');
            });
        }
        setTimeout(() => el.find('.form-control').removeClass('invalid'), 6000);
        return message;
    };

    const validate = (attr, target, regex, errorMessage, out = false) => {
        if (!regex.test(attr)) {
            return showError(target, errorMessage, out);
        }
        return false;
    };

    const isPhone = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;
        const telefonoRegex = /^([0-9]){7,10}$/;
        return validate(attr, target, telefonoRegex, `El campo ${label} debe ser un valor válido.`, out);
    };

    const isNumeric = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;
        const numericoRegex = /^([0-9]+){0,20}$/;
        return validate(attr, target, numericoRegex, `El campo ${label} debe ser un valor válido.`, out);
    };

    const hasSpaces = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;
        const espaciosRegex = /\s/;
        if (espaciosRegex.test(attr)) {
            return showError(target, `El campo ${label} no puede contener espacios.`, out);
        }
        return false;
    };

    const hasEmpty = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;
        if (attr === '' || attr === void 0 || attr === undefined || attr === null) {
            return showError(target, `El campo ${label} no puede estar indefinido.`, out);
        }
        return false;
    };

    const isEmail = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;
        const emailRegex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return validate(attr, target, emailRegex, `La dirección de ${label} no es válida.`, out);
    };

    const isIdentification = (transfer = {}) => {
        const { attr, target, min = 1, max = 100, out = false, label = '' } = transfer;

        const numericoRegex = /^([0-9]+){1,100}$/;
        if (!numericoRegex.test(attr)) {
            return showError(target, `El campo ${label} debe ser un valor válido.`, out);
        } else {
            const express = new RegExp('^([0-9]+){' + min + ',' + max + '}', 'i');
            if (!express.test(attr)) {
                return showError(target, `El campo ${label} debe ser un valor entre ${min} y ${max} dígitos.`, out);
            }
        }
        return false;
    };

    const maxSize = (transfer = {}) => {
        const { attr, target, max = 225, out = false, label = '' } = transfer;
        if (!_.isUndefined(attr)) {
            if (attr.toString().length > max) {
                return showError(target, `El campo ${label} no puede tener ${max} número de caracteres.`, out);
            }
        }
    };

    const isDate = (transfer = {}) => {
        const { attr, target, out = false, label = '' } = transfer;

        const __isDate = function (fecha) {
            const regexFecha = /^\d{4}-\d{2}-\d{2}$/;

            if (!fecha.match(regexFecha)) {
                return false;
            }

            const fechaDividida = fecha.split('-');
            const year = parseInt(fechaDividida[0], 10);
            const month = parseInt(fechaDividida[1], 10);
            const day = parseInt(fechaDividida[2], 10);

            if (month < 1 || month > 12) {
                return false;
            }
            if (day < 1 || day > 31) {
                return false;
            }
            if ((month === 4 || month === 6 || month === 9 || month === 11) && day === 31) {
                return false;
            }
            if (month === 2) {
                const esBisiesto = (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
                if (day > 29 || (day === 29 && !esBisiesto)) {
                    return false;
                }
            }
            return true;
        };
        const res = __isDate(attr);
        if (res) {
            return false;
        } else {
            return showError(target, `El campo ${label} debe ser un valor de fecha valido.`, out);
        }
    };

    return {
        espacio: hasSpaces,
        numerico: isNumeric,
        vacio: hasEmpty,
        email: isEmail,
        telefono: isPhone,
        identi: isIdentification,
        date: isDate,
        max: maxSize,
    };
})();

const langDataTable = {
    processing: 'Procesando...',
    lengthMenu: 'Mostrar _MENU_ resultados por pagínas',
    zeroRecords: 'No se encontraron resultados',
    info: 'Mostrando pagína _PAGE_ de _PAGES_.\tTotal de _TOTAL_ registros.',
    infoEmpty: 'No records available',
    infoFiltered: '(filtered from _MAX_ total records)',
    emptyTable: 'Ningún dato disponible en esta tabla',
    search: 'Buscar',
    paginate: {
        next: 'siguiente',
        previus: 'anterior',
        first: 'primero',
        last: 'ultimo',
    },
    loadingRecords: 'Cargando...',
    buttons: {
        copy: 'Copiar',
        colvis: 'Visibilidad',
        collection: 'Colección',
        colvisRestore: 'Restaurar visibilidad',
        copyKeys:
            'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
        copySuccess: {
            1: 'Copiada 1 fila al portapapeles',
            _: 'Copiadas %d fila al portapapeles',
        },
    },
};

function handleFiles() {
    const _el = document.querySelector("[data-toggle='upload']");
    if (_el.files.length == 0) return false;

    const test = /(\.docx\.doc|\.pdf|\.jpg|\.png|\.jpeg)$/i;
    const fpath = _el.files[0]['name'];
    if (!test.exec(fpath)) {
        document.getElementById('name_archivo').textContent = '';
        document.getElementById('remover_archivo').setAttribute('disabled', true);
        document.getElementById('bt_hacer_cargue').setAttribute('disabled', true);

        alert('Please upload file having extensions .pdf, .docx, .doc, .png, .jpg, .jpeg only.');
        _el.value = '';
        return false;
    } else {
        if (_el.files && _el.files[0]) {
            const _exp = fpath.split('_');
            fpath = _exp.join('\r');
            document.getElementById('name_archivo').textContent = fpath;
            document.getElementById('remover_archivo').removeAttribute('disabled');
            document.getElementById('bt_hacer_cargue').removeAttribute('disabled');
        }
    }
}

const eventsFormControl = (el) => {
    el.find('input.form-control').each(function () {
        if ($.trim(this.value).length == 0) {
            $(this).siblings('.control-label').removeClass('top');
        } else {
            $(this).siblings('.control-label').addClass('top');
        }
    });

    el.find('input.form-control').on('click', function () {
        $(this).siblings('.control-label').addClass('top');
    });

    el.find('label.control-label').on('click', function () {
        if ($(this).hasClass('top') === false) $(this).addClass('top');
    });

    el.find('input.form-control').focus(function () {
        $(this).siblings('.control-label').addClass('top');
    });

    el.find('input.form-control').blur(function () {
        if ($.trim(this.value).length == 0) {
            $(this).siblings('.control-label').removeClass('top');
        } else {
            $(this).siblings('.control-label').addClass('top');
        }
    });

    return false;
};

export { Testeo, capitalize, eventsFormControl, handleFiles, is_date, is_date_general, is_email, is_numeric, langDataTable };
