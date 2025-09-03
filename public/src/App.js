import { Region } from '@/Common/Region';
import loading from '@/Componentes/Views/Loading';
import { $Kumbia } from '@/Utils';

const $App = {
    Models: {},
    Collections: {},
    router: null,
    currentSubapp: null,
    Modal: null,
    layout: null,
    el: null,
    mainRegion: null,
    startApp(RouterModule, ruta = '', el = '') {
        this.el = el;
        if (this.el === '') {
            this.el = '#contentView';
        }
        this.initialize();
        this.router = new RouterModule();
        if (!Backbone.history.start()) {
            this.router.navigate(ruta, { trigger: true });
        }
    },
    initialize() {
        _.extend(this, Backbone.Events);

        const alertTypes = ['error', 'warning', 'success', 'info'];

        alertTypes.forEach((type) => {
            this.listenTo(this, `noty:${type}`, (message) => this.notify(type, message));
            this.listenTo(this, `alert:${type}`, (transfer) => this.alert(type, transfer));
        });

        this.listenTo(this, 'confirma', this.confirmaApp);
        this.listenTo(this, 'syncro', this.syncroRequest);
        this.listenTo(this, 'ajax', this.ajaxKumbia);
        this.listenTo(this, 'show:modal', this.renderModal);
        this.listenTo(this, 'hide:modal', this.closeModal);
        this.listenTo(this, 'down', this.downLoadFile);
        this.listenTo(this, 'upload', this.uploadFile);
    },
    notify(type, message) {
        this.showNoty(type, message.toString(), type === 'error' ? 10000 : 6000);
    },
    alert(type, transfer) {
        const { title = 'Notificación', message = 'Nota no hay respuesta de la solicitud', timer = 8200 } = transfer;
        this.showAlert(title, message, type, timer);
    },
    showNoty(type, message, timeout) {
        new Noty({
            text: message,
            layout: 'topRight',
            theme: 'relax',
            type: type,
            timeout: timeout,
        }).show();
    },
    showAlert(title = '', text = '', icon = '', timer) {
        Swal.fire({
            title,
            html: text,
            icon,
            showConfirmButton: false,
            confirmButtonText: 'Continuar',
            timer,
        });
    },
    startSubApplication(SubApplication, __services, options = {}) {
        let services = [];

        if (typeof __services != 'string') {
            if (_.isArray(__services) === true) {
                _.each(__services, (ser) => {
                    services[ser.name] = new ser.inyectar();
                });
            } else if (_.isObject(__services) === true) {
                services[__services.name] = new __services.inyectar();
            } else {
                services.push(__services);
            }
        }

        this.mainRegion = new Region({ el: this.el, ...options });
        this.currentSubapp = new SubApplication({
            region: this.mainRegion,
            services: services,
            App: this,
        });
        return this.currentSubapp;
    },
    confirmaApp(transfer) {
        const { message, callback, title = '¿Confirmar?', icon = 'warning' } = transfer;
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#fc8c72',
            confirmButtonText: 'SI, Continuar!',
        }).then((result) => {
            callback(result.isConfirmed);
        });
    },
    syncroRequest(transfer) {
        const { url, data = {}, callback, silent = false } = transfer;
        const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');

        Backbone.ajax({
            type: 'POST',
            dataType: 'json',
            url: url,
            data: data,
            cache: false,
            contentType: 'application/x-www-form-urlencoded',
            processData: true,
            timeout: 52400,
            beforeSend: (xhr) => {
                if (silent == false) loading.show();
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                if (csrf.length > 0) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                    xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
                }
            },
            success: (response) => {
                if (silent == false) loading.hide();
                return callback(response);
            },
            error: (err) => {
                const keys = _.keys(err);
                if (silent == false) loading.hide();
                if (_.indexOf(keys, 'responseText') !== -1) {
                    console.log(err.responseText);
                    this.trigger('alert:error', { message: err.responseText });
                } else {
                    console.log(err);
                    this.trigger('alert:error', { message: err });
                }
                return callback(false);
            },
        });
    },
    renderModal(transfer = {}) {
        const { title, view, options = false } = transfer;
        const targetModal = $('#modalComponent');
        if (!this.Modal) {
            this.Modal = new bootstrap.Modal(document.getElementById('modalComponent'), {
                keyboard: true,
                backdrop: 'static',
            });
        }
        targetModal.find('#mdl_set_title').text(title);
        targetModal.find('#mdl_set_footer').css('display', 'none');
        if (options) {
            targetModal.find('.modal-dialog').addClass(options.bootstrapSize);
        }
        targetModal.find('#mdl_set_body').html(view.render().$el);
        this.Modal.show();
        if (options) {
            this.Modal.on('hidden.bs.modal', (event) => view.remove());
            targetModal.find('.close').addClass('d-none');
        }
    },
    closeModal(view) {
        if (this.Modal) {
            this.Modal.hide();
            this.Modal = null;
        }
        if (view) {
            view.remove();
        }
    },
    url(method = '', controller = undefined) {
        return this.kumbiaURL((controller ?? '') + '/' + method);
    },
    kumbiaURL: (url) => {
        if (_.isUndefined(url) === true) url = '';
        return $Kumbia.path + url;
    },
    ajaxKumbia(transfer = {}) {
        const { url, data = {}, callback, silent = false } = transfer;
        // eslint-disable-next-line quotes
        const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');

        $.ajax({
            type: 'POST',
            data: data,
            url: this.kumbiaURL(url),
            dataType: 'html',
            processData: true,
            contentType: 'application/x-www-form-urlencoded',
            cache: false,
            beforeSend: (xhr) => {
                if (silent == false) loading.show();
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                if (csrf.length > 0) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
                }
            },
        })
            .done((response) => {
                if (silent == false) loading.hide();
                return callback(response);
            })
            .fail((err) => {
                if (silent == false) loading.hide();
                this.trigger('alert:error', { message: err.responseText });
                return callback(false);
            })
            .always(() => {
                if (silent == false) loading.hide();
            });
    },
    downLoadFile(transfer) {
        const { url, filename } = transfer;
        const link = document.createElement('a');
        link.href = this.kumbiaURL(url + '/' + filename);
        link.download = filename;
        console.log(link);
        link.click();
    },
    uploadFile(transfer) {
        const { url, data, callback, silent = false } = transfer;
        let formData;
        if (data instanceof FormData) {
            formData = data;
        } else {
            formData = new FormData();
            formData.append(data.name, data.file);
        }
        // eslint-disable-next-line quotes
        const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
        Backbone.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: (xhr) => {
                if (silent == false) loading.show();
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                if (csrf.length > 0) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
                }
            },
            success: (response) => {
                if (silent == false) loading.hide();
                return callback(response);
            },
            error: (err) => {
                const keys = _.keys(err);
                console.log(err);
                if (silent == false) loading.hide();
                if (_.indexOf(keys, 'responseText') !== -1) {
                    this.trigger('alert:error', { message: err.responseText });
                } else {
                    this.trigger('alert:error', { message: err });
                }
                return callback(false);
            },
        });
    },
};

export { $App };
