import { $App } from '@/App';
import { Region } from '@/Common/Region';
import loading from '@/Componentes/Views/Loading';
import LayoutLogin from './views/LayoutLogin';
import VerificationView from './views/VerificationView';

export default class Verification {
    #region = null;
    #layout = null;
    #App = null;

    constructor(options = {}) {
        _.extend(this, Backbone.Events);
        this.#App = options.App || $App;
        this.#region = options.region;
        this.#layout = new LayoutLogin({
            model: {
                useInfo: true,
            },
        });
        this.#region.show(this.#layout);
        this.on('form:cancel', this.destroy);
    }

    main(params = {}) {
        loading.hide();
        const view = new VerificationView({ model: params });
        this.listenTo(view, 'send:verify', this.__procesarVerify);
        this.#layout.getRegion('verification').show(view);

        $('#render_register').fadeOut('fast');
        $('#render_sesion').fadeOut('fast');
    }

    __procesarVerify({ data, callback }) {
        this.#App.trigger('syncro', {
            url: this.#App.url('mercurio/verify'),
            data,
            callback: (response = undefined) => {
                if (response && response.success === true) {
                    return callback(response);
                } else {
                    this.#App.trigger('alert:error', { message: response.msj });
                }
                return callback(false);
            },
        });
    }

    destroy() {
        this.stopListening();
        if (this.#region && this.#region instanceof Region) this.#region.remove();
    }
}
