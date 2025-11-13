export class ModalController {
    constructor(rootId = 'modalComponent', app = window.App) {
        this.rootId = rootId;
        this.app = app;
        this.instance = null; // bootstrap.Modal instance
        this.dynamicClasses = '';
        this._boundHidden = null;
        this._boundShown = null;
    }

    get modalEl() {
        return document.getElementById(this.rootId);
    }

    get $modal() {
        return $(`#${this.rootId}`);
    }

    _ensureInstance(options = {}) {
        const { keyboard = false, backdrop = 'static' } = options;
        if (!this.instance) {
            this.instance = new bootstrap.Modal(this.modalEl, { keyboard, backdrop });
        } else {
            // Actualizar opciones si cambiaron
            this.instance._config.keyboard = keyboard;
            this.instance._config.backdrop = backdrop;
        }
    }

    _applyDialogClasses(options = {}) {
        const { size, bootstrapSize, centered, scrollable, dialogClass } = options;
        const $dialog = this.$modal.find('.modal-dialog');
        // limpiar previas
        if (this.dynamicClasses) {
            $dialog.removeClass(this.dynamicClasses);
            this.dynamicClasses = '';
        }
        const classes = [];
        const resolvedSize = size || bootstrapSize; // compatibilidad
        if (resolvedSize) classes.push(resolvedSize);
        if (centered) classes.push('modal-dialog-centered');
        if (scrollable) classes.push('modal-dialog-scrollable');
        if (dialogClass) classes.push(dialogClass);
        this.dynamicClasses = classes.filter(Boolean).join(' ');
        if (this.dynamicClasses) $dialog.addClass(this.dynamicClasses);
    }

    _renderFooter(footer, view) {
        const $footer = this.$modal.find('#mdl_set_footer');
        if (!footer) {
            $footer.hide().empty();
            return;
        }
        $footer.show().empty();
        if (_.isArray(footer)) {
            footer.forEach((btn) => {
                const $b = $(`<button type="button" class="btn ${btn.className || 'btn-secondary'}"></button>`);
                $b.text(btn.text || 'Acción');
                if (btn.id) $b.attr('id', btn.id);
                $b.on('click', () => btn.onClick && btn.onClick(view, this.app));
                $footer.append($b);
            });
        } else if (footer && typeof footer.render === 'function') {
            $footer.html(footer.render().$el);
        }
    }

    show(transfer = {}) {
        const { title = '', view, options = {} } = transfer;
        if (!view || typeof view.render !== 'function') return;

        const {
            bodyClass = '',
            showClose = true,
            ariaLabelledBy,
            autoFocus,
            onShown,
            onHidden,
            destroyOnClose = true,
        } = options;

        this._ensureInstance(options);
        this._applyDialogClasses(options);

        const $target = this.$modal;
        $target.find('#mdl_set_title').text(title);
        $target.find('.close, [data-bs-dismiss="modal"]').toggleClass('d-none', !showClose);
        if (ariaLabelledBy) $target.attr('aria-labelledby', ariaLabelledBy);

        const $body = $target.find('#mdl_set_body');
        if (bodyClass) $body.addClass(bodyClass);
        $body.html(view.render().$el);

        this._renderFooter(options.footer, view);

        // Bind eventos de ciclo de vida
        const el = this.modalEl;
        if (this._boundShown) el.removeEventListener('shown.bs.modal', this._boundShown);
        if (this._boundHidden) el.removeEventListener('hidden.bs.modal', this._boundHidden);

        this._boundShown = () => {
            if (autoFocus) this.$modal.find(autoFocus).trigger('focus');
            if (typeof onShown === 'function') onShown(el, view);
        };
        this._boundHidden = () => {
            if (destroyOnClose && view && typeof view.remove === 'function') view.remove();
            if (bodyClass) $body.removeClass(bodyClass);
            if (this.dynamicClasses) this.$modal.find('.modal-dialog').removeClass(this.dynamicClasses), (this.dynamicClasses = '');
            if (typeof onHidden === 'function') onHidden(el, view);
            this.instance = null;
        };
        el.addEventListener('shown.bs.modal', this._boundShown, { once: true });
        el.addEventListener('hidden.bs.modal', this._boundHidden, { once: true });

        this.instance.show();
    }

    hide(view) {
        if (this.instance) this.instance.hide();
        if (view && typeof view.remove === 'function') view.remove();
    }
}
