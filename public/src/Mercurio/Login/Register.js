import { $App } from '@/App';
import { Region } from '@/Common/Region';
import loading from '@/Componentes/Views/Loading';
import { is_email } from '@/Core';
import LayoutLogin from './views/LayoutLogin';
import RegisterView from './views/RegisterView';

export default class Register {
    #App = null;
    #region = null;
    #layout = null;

    constructor(options = {}) {
        _.extend(this, Backbone.Events);
        sessionStorage.setItem('miTokenAuth', '');
        this.#App = options.App || $App;
        this.#region = options.region;
        this.#layout = new LayoutLogin({
            model: {
                useInfo: false,
            },
        });
        this.#region.show(this.#layout);
        this.on('form:cancel', this.destroy);
    }

    main(formComponents) {
        const view = new RegisterView({ collection: formComponents });
        this.listenTo(view, 'send:register', this.__registerServer);
        this.listenTo(view, 'load:captcha', this.__reloadCaptcha);

        this.#layout.getRegion('register').show(view);
        $('#render_sesion').fadeOut();
    }

    __registerServer(transfer = {}) {
        const { callback, data, token } = transfer;
        $.ajax({
            method: 'POST',
            dataType: 'JSON',
            cache: false,
            url: this.#App.url('mercurio/registro'),
            data: data,
            beforeSend: (xhr) => {
                loading.show();
                xhr.setRequestHeader('Authorization', 'Bearer ' + token);
            },
        })
            .done((response) => {
                loading.hide();
                if (response.success) {
                    return callback(response);
                } else {
                    this.#App.trigger('alert:error', { message: response.msj });
                }
                return callback(false);
            })
            .fail((err) => {
                loading.hide();
                this.#App.trigger('alert:error', { message: err.responseText });
                return callback(false);
            });
    }

    __validaEmail() {
        let _email = $('#email').val();
        let _cedrep = $('#cedrep').val();
        let _nit = $('#nit').val();

        if (_cedrep == '') {
            this.#App.trigger('alert:warning', {
                message: 'Ingresa primero el número de documento del representante legal.',
            });
            $('#cedrep').focus();
            return false;
        }

        if (_nit == '') {
            this.#App.trigger('alert:warning', {
                message: 'Ingresa primero el nit de la empresa.',
            });
            $('#nit').focus();
            return false;
        }

        if (!is_email(_email)) {
            $('#email-error').text('Debe tener formato de email correcto.');
            $('#email-error').attr('style', 'display:inline-block');
            return false;
        }

        this.#App.trigger('syncro', {
            url: this.#App.url('mercurio/valida_email'),
            data: {
                email: _email,
                documento: _cedrep,
                nit: _nit,
            },
            callback: (response) => {
                if (response) {
                    if (response.success) {
                    } else {
                        this.#App.trigger('alert:warning', { message: response.msj });
                        $('#email').val('');
                        return;
                    }
                }
            },
        });
    }

    __reloadCaptcha() {
        document.getElementById('reloadCaptcha').addEventListener('click', function (event) {
            event.preventDefault();
            const url = $App.url('captcha/' + Math.random());
            $('#captchaImage').attr('src', url.toString());
            this.blur();
            return false;
        });
    }

    destroy() {
        this.stopListening();
        if (this.#region && this.#region instanceof Region) this.#region.remove();
    }
}
