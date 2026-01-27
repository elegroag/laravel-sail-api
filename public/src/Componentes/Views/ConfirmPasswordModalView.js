import confirm_password_template from '@/Componentes/Views/Templates/confirm_password.hbs?raw';

export default class ConfirmPasswordModalView extends Backbone.View {
    constructor(options = {}) {
        super(options);

        // Opciones por defecto
        this.options = _.extend(
            {
                title: 'Confirmación requerida',
                message: 'Ingrese su clave para confirmar el envío de la información.',
                inputPlaceholder: 'Ingrese su contraseña',
                confirmText: 'Continuar',
                cancelText: 'Cancelar',
                showStrengthIndicator: false,
                inputAttributes: {
                    autocapitalize: 'off',
                },
                onConfirm: null, // Callback cuando se confirma con la contraseña
                onCancel: null, // Callback cuando se cancela
            },
            options || {},
        );

        this.template = _.template(confirm_password_template);
        this.passwordValue = null;
    }

    render() {
        this.$el.html(`
            <div class="confirm-password-modal-view">
                ${this.template({
                    ...this.options,
                })}
            </div>
        `);
        return this;
    }

    get events() {
        return {
            'input .digit-input': 'handleDigitInput',
            'keydown .digit-input': 'handleKeydown',
            'paste .digit-input': 'handlePaste',
        };
    }

    /**
     * Maneja la entrada de dígitos individuales
     */
    handleDigitInput(e) {
        const input = $(e.currentTarget);
        const value = input.val();
        const index = parseInt(input.data('index'));

        // Permitir solo números
        const numericValue = value.replace(/[^0-9]/g, '');

        if (numericValue !== value) {
            input.val(numericValue);
            return;
        }

        // Si hay un valor, mover al siguiente input
        if (numericValue && index < 5) {
            const nextInput = this.$el.find(`#digit${index + 1}`);
            nextInput.focus();
        }

        // Actualizar el campo oculto con la clave completa
        this.updateHiddenPassword();

        // Limpiar validación
        input.removeClass('is-invalid');
    }

    /**
     * Maneja eventos de teclado para navegación
     */
    handleKeydown(e) {
        const input = $(e.currentTarget);
        const index = parseInt(input.data('index'));

        // Manejar teclas especiales
        switch (e.key) {
            case 'Backspace':
            case 'Delete':
                if (input.val() === '' && index > 0) {
                    // Mover al input anterior si está vacío
                    e.preventDefault();
                    const prevInput = this.$el.find(`#digit${index - 1}`);
                    prevInput.focus().val('');
                    this.updateHiddenPassword();
                }
                break;
            case 'ArrowLeft':
                if (index > 0) {
                    e.preventDefault();
                    this.$el.find(`#digit${index - 1}`).focus();
                }
                break;
            case 'ArrowRight':
                if (index < 5) {
                    e.preventDefault();
                    this.$el.find(`#digit${index + 1}`).focus();
                }
                break;
            case 'Tab':
                // Permitir navegación normal con Tab
                break;
            default:
                // Prevenir caracteres no numéricos
                if (e.key.length === 1 && !/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
                break;
        }
    }

    /**
     * Maneja el evento de pegar
     */
    handlePaste(e) {
        e.preventDefault();
        const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        const numericData = pastedData.replace(/[^0-9]/g, '').slice(0, 6);

        // Distribuir los dígitos en los inputs
        const digits = numericData.split('');
        digits.forEach((digit, index) => {
            if (index < 6) {
                this.$el.find(`#digit${index}`).val(digit);
            }
        });

        // Enfocar el siguiente input vacío o el último si todos están llenos
        const nextEmptyIndex = digits.length;
        if (nextEmptyIndex < 6) {
            this.$el.find(`#digit${nextEmptyIndex}`).focus();
        } else {
            this.$el.find('#digit5').focus();
        }

        this.updateHiddenPassword();
    }

    /**
     * Actualiza el campo oculto con la clave completa
     */
    updateHiddenPassword() {
        const digits = [];
        for (let i = 0; i < 6; i++) {
            const digit = this.$el.find(`#digit${i}`).val();
            digits.push(digit);
        }
        const password = digits.join('');
        this.$el.find('#passwordInput').val(password);
    }

    /**
     * Maneja la cancelación
     */
    handleCancel(e) {
        if (e) e.preventDefault();

        // Ejecutar callback de cancelación
        if (_.isFunction(this.options.onCancel)) {
            this.options.onCancel();
        }
    }

    /**
     * Limpia y remueve la vista
     */
    remove() {
        this.stopListening();
        Backbone.View.prototype.remove.call(this);
    }
}
