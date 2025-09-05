import { $App } from '@/App';
import { HeaderView } from '@/Componentes/Views/HeaderView';

class AfiliationService {
    constructor() {
        this.headerView = null;
    }

    findDataTable(transfer = {}) {
        const { url, callback, silent = false } = transfer;
        $App.trigger('ajax', {
            url: url,
            data: {},
            silent,
            callback: (response) => {
                if (response) {
                    if (response) {
                        return callback(response);
                    }
                }
                return callback(false);
            },
        });
    }

    paramsServer(transfer = {}) {
        const callback = transfer.callback || false;
        const silent = transfer.silent || false;

        $App.trigger('syncro', {
            url: $App.url('params', window.ServerController ?? 'principal'),
            data: {},
            silent,
            callback: (response) => {
                if (response) {
                    if (response.success === true) {
                        if (_.isNull($App.Collections.formParams)) $App.Collections.formParams = [];
                        _.extend($App.Collections.formParams, response.data);
                        return callback !== false ? callback(response.msj) : '';
                    }
                }
                return callback !== false ? callback(false) : '';
            },
        });
    }

    serachRequestServer(transfer = {}) {
        const { id, callback } = transfer;
        $App.trigger('syncro', {
            url: $App.url('searchRequest/' + id, window.ServerController ?? 'principal'),
            data: {},
            callback: (response) => {
                if (response) {
                    return callback(response);
                }
                return callback(false);
            },
        });
    }

    sendRadicado(transfer = {}) {
        const { model, callback } = transfer;
        $App.trigger('syncro', {
            url: $App.url('enviarCaja', window.ServerController ?? 'principal'),
            data: model.toJSON(),
            callback: (response) => {
                if (response) {
                    if (response.success) {
                        $App.trigger('alert:success', { message: response.msj });
                        return callback(response);
                    } else {
                        $App.trigger('alert:error', { message: response.msj });
                    }
                }
                return callback(false);
            },
        });
    }

    initHeaderView(options = {}) {
        let estado_detalle;
        if (this.headerView) this.headerView.remove();
        switch (options.estado) {
            case 'T':
                estado_detalle = 'Temporales';
                break;
            case 'P':
                estado_detalle = 'Pendientes';
                break;
            case 'D':
                estado_detalle = 'Devueltos';
                break;
            case 'R':
                estado_detalle = 'Rechazados';
                break;
            case 'A':
                estado_detalle = 'Aprobados';
                break;
            default:
                estado_detalle = '';
                break;
        }

        this.headerView = new HeaderView({
            model: {
                ...options,
                estado_detalle,
            },
        });

        $App.layout.getRegion('header').show(this.headerView);
        $("[data-toggle='linkFilter'][data-valor='" + options.estado + "']").addClass('active');
    }

    digitVer(_data = {}) {
        const { nit, callback } = _data;
        $App.trigger('syncro', {
            url: $App.url('digito_verification', window.ServerController ?? 'principal'),
            data: { documento: nit, nit: nit },
            callback: (response) => {
                if (response) {
                    return callback(response);
                }
                return callback(false);
            },
        });
    }

    validePk(transfer = {}) {
        const { data, callback } = transfer;
        $App.trigger('syncro', {
            url: $App.url('valida', window.ServerController ?? 'principal'),
            data: data,
            silent: true,
            callback: (response) => {
                if (response) {
                    if (response.success) {
                        let solicitud = null;

                        if (response.solicitud_previa !== false) {
                            solicitud = response.solicitud_previa;
                        }
                        if (response.empresa !== false) {
                            solicitud = response.empresa;
                        }
                        if (response.trabajador !== false) {
                            solicitud = response.trabajador;
                        }
                        if (_.isNull(solicitud)) return false;

                        return callback(solicitud);
                    } else {
                        $App.trigger('alert:error', { message: response.msj });
                    }
                }
                return callback(false);
            },
        });
    }

    saveFormData(transfer = {}) {
        const { entity, callback } = transfer;

        $App.trigger('syncro', {
            url: $App.url('guardar', window.ServerController ?? 'principal'),
            data: entity.toJSON(),
            callback: (response) => {
                if (response) {
                    return callback(response);
                }
                return callback(false);
            },
        });
    }

    cancelaSolicitud(transfer = {}) {
        const { id, callback } = transfer;

        $App.trigger('confirma', {
            message: 'Se requiere de confirmar que desear borra la solicitud de afiliación.',
            callback: (status) => {
                if (status) {
                    $App.trigger('syncro', {
                        url: $App.url('borrar', window.ServerController ?? 'principal'),
                        data: { id },
                        callback: (response) => {
                            if (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Notificación',
                                        text: response.msj,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 10000,
                                    });
                                    return callback(true);
                                } else {
                                    Swal.fire({
                                        title: 'Notificación Error',
                                        text: response.msj,
                                        icon: 'warning',
                                        showConfirmButton: false,
                                        timer: 10000,
                                    });
                                }
                            }
                            return callback(false);
                        },
                    });
                }
            },
        });
    }

    validaFirmas(callback) {
        $App.trigger('syncro', {
            url: $App.kumbiaURL('firmas/show'),
            data: {},
            callback: (response) => {
                if (response) {
                    if (response.success) {
                        return callback(response);
                    } else {
                        $App.trigger('alert:warning', {
                            message: 'No se puede generar el formulario',
                        });
                    }
                }
                return callback(false);
            },
        });
    }
}

export { AfiliationService };
