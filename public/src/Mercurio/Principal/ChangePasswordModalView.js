import tmp_change_password from './tmp/tmp_change_password.hbs?raw';

export default class ChangePasswordModalView extends Backbone.View {
    constructor(options = {}) {
        super(options);
        this.template = _.template(tmp_change_password);
        this.App = options.App;
    }

    render() {
        this.$el.html(this.template());
        return this;
    }

    get events() {
        return {
            'click #btn-toggle-password': 'togglePasswordVisibility',
            'click #btn-generate-password': 'generateSecurePassword',
        };
    }

    togglePasswordVisibility(e) {
        e.preventDefault();
        const $btn = this.$(e.currentTarget);
        const $input = this.$('#clave');
        const currentType = $input.attr('type') || 'password';
        if (currentType === 'password') {
            $input.attr('type', 'text');
            $btn.html('<i class="fa fa-eye"></i>');
        } else {
            $input.attr('type', 'password');

            $btn.html('<i class="fa fa-eye-slash"></i>');
        }
    }

    generateSecurePassword() {
        const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lowercase = 'abcdefghijklmnopqrstuvwxyz';
        const numbers = '0123456789';
        const symbols = '$&@*#_-';
        const all = uppercase + lowercase + numbers + symbols;

        let password = '';
        password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
        password += symbols.charAt(Math.floor(Math.random() * symbols.length));
        password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
        password += numbers.charAt(Math.floor(Math.random() * numbers.length));

        const targetLength = 12;
        for (let i = password.length; i < targetLength; i++) {
            password += all.charAt(Math.floor(Math.random() * all.length));
        }

        password = password
            .split('')
            .sort(() => Math.random() - 0.5)
            .join('');

        const $newPassword = this.$('#clave');
        const $confirmPassword = this.$('#clacon');
        $newPassword.val(password);
        $confirmPassword.val(password);
    }

    submit(app) {
        const $form = this.$('#formChangePassword');
        const $newPassword = $form.find('#clave');
        const $confirmPassword = $form.find('#clacon');

        const clave = ($newPassword.val() || '').trim();
        const clacon = ($confirmPassword.val() || '').trim();

        const errors = [];

        if (!clave) {
            errors.push('La nueva clave es obligatoria.');
        }

        if (!clacon) {
            errors.push('La confirmación de la nueva clave es obligatoria.');
        }

        if (clave && clave.length < 8) {
            errors.push('La nueva clave debe tener al menos 8 caracteres.');
        }

        if (clave && !/[A-Z]/.test(clave)) {
            errors.push('La nueva clave debe contener al menos una letra mayúscula.');
        }

        if (clave && !/[^A-Za-z0-9]/.test(clave)) {
            errors.push('La nueva clave debe contener al menos un símbolo.');
        }

        if (clave && clacon && clave !== clacon) {
            errors.push('La confirmación de la nueva clave no coincide.');
        }

        if (errors.length > 0) {
            app.trigger('alert:error', { message: errors.join('<br/>') });

            if (!claant) {
                $oldPassword.trigger('focus');
            } else if (!clave || clave.length < 8 || !/[A-Z]/.test(clave) || !/[^A-Za-z0-9]/.test(clave)) {
                $newPassword.trigger('focus');
            } else if (!clacon || clave !== clacon) {
                $confirmPassword.trigger('focus');
            }
            return;
        }

        Swal.fire({
            text: '¿Está seguro de cambiar la clave de acceso?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2dce89',
            cancelButtonColor: '#fc8c72',
            confirmButtonText: 'SI, Cambiar clave',
            cancelButtonText: 'NO',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            window.App.trigger('syncro', {
                url: window.App.url('principal/cambio_clave'),
                data: {
                    clave,
                    clacon,
                },
                callback: (response) => {
                    if (response && response.success) {
                        const success = response && response.success === true;
                        const message =
                            (response && (response.msg || response.msj)) ||
                            (success ? 'La clave se actualizó correctamente.' : 'No fue posible cambiar la clave. Intenta nuevamente.');

                        if (success) {
                            window.App.trigger('alert:success', { message });
                            window.App.trigger('hide:modal', this);
                        } else {
                            window.App.trigger('alert:error', { message });
                        }

                        this.remove();
                        window.App.router.navigate('list', { trigger: true, replace: true });
                    } else {
                        console.error('Error al cambiar la clave:', response);
                        window.App.trigger('alert:error', {
                            message: response.msj || 'No fue posible cambiar la clave por un error de comunicación. Intenta nuevamente.',
                        });
                    }
                },
            });
        });
    }
}
