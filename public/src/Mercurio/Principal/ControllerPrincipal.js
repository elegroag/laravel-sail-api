import ChangePasswordModalView from './ChangePasswordModalView';
import { PrincipalLayout } from './PrincipalLayout';
import { ServiciosView } from './ServiciosView';
import { TotalesView } from './TotalesView';

/**
 * ControllerPrincipal
 * Controlador principal con carga dinámica de secciones
 */
class ControllerPrincipal {
    constructor(options) {
        this.App = null;
        this.region = null;
        this.layout = null;

        _.extend(this, options);
        _.extend(this, Backbone.Events);
        this.once('syncro:request', this.__syncroSolicitudes);

        this.layout = new PrincipalLayout();
        this.listenTo(this.layout, 'form:cancel', this.destroy);

        // Escuchar eventos de sección visible para carga lazy
        this.listenTo(this.layout, 'section:visible', this.__onSectionVisible);

        this.region.show(this.layout);
    }

    /**
     * Lista los servicios y carga el contenido dinámicamente
     */
    listServices() {
        this.__validaSyncro();
        this.__buscarServicios({
            callback: (response) => {
                if (response) {
                    // Cargar sección de totales
                    this.__loadTotalesSection(response.totales);

                    // Cargar sección de afiliaciones
                    this.__loadAfiliacionesSection();

                    // Cargar sección de consultas
                    this.__loadConsultasSection();

                    // Cargar sección de productos
                    this.__loadProductosSection();
                }
            },
            silent: false,
        });
    }

    /**
     * Carga la sección de totales con animación
     * @param {Object} totales - Datos de totales
     */
    __loadTotalesSection(totales) {
        if (!totales) return;

        const view = new TotalesView({ model: totales });
        this.layout.getRegion('totales').append(view);

        // Marcar sección como cargada
        this.layout.markSectionLoaded('totales');
    }

    /**
     * Carga la sección de afiliaciones con animación escalonada
     */
    __loadAfiliacionesSection() {
        if (!this.App.Collections.afiliacion) return;

        const region = this.layout.getRegion('afiliaciones');

        // Cargar cada card con delay para animación escalonada
        this.App.Collections.afiliacion.forEach((item, index) => {
            setTimeout(() => {
                item.tipo = 'afiliacion';
                const view = new ServiciosView({ model: item });
                region.append(view);
            }, index * 100); // 100ms de delay entre cada card
        });

        // Marcar sección como cargada
        setTimeout(
            () => {
                this.layout.markSectionLoaded('afiliaciones');
            },
            this.App.Collections.afiliacion.length * 100 + 100,
        );
    }

    /**
     * Carga la sección de consultas con animación escalonada
     */
    __loadConsultasSection() {
        if (!this.App.Collections.consultas) return;

        const region = this.layout.getRegion('consultas');

        // Cargar cada card con delay para animación escalonada
        this.App.Collections.consultas.forEach((item, index) => {
            setTimeout(() => {
                item.tipo = 'consultas';
                const view = new ServiciosView({ model: item });
                region.append(view);
            }, index * 100);
        });

        // Marcar sección como cargada
        setTimeout(
            () => {
                this.layout.markSectionLoaded('consultas');
            },
            this.App.Collections.consultas.length * 100 + 100,
        );
    }

    /**
     * Carga la sección de productos con animación escalonada
     */
    __loadProductosSection() {
        if (!this.App.Collections.productos) return;

        const region = this.layout.getRegion('productos');

        // Cargar cada card con delay para animación escalonada
        this.App.Collections.productos.forEach((item, index) => {
            setTimeout(() => {
                item.tipo = 'productos';
                const view = new ServiciosView({ model: item });
                region.append(view);
            }, index * 100);
        });

        // Marcar sección como cargada
        setTimeout(
            () => {
                this.layout.markSectionLoaded('productos');
            },
            this.App.Collections.productos.length * 100 + 100,
        );
    }

    /**
     * Callback cuando una sección se hace visible (para carga lazy futura)
     * @param {Object} data - Datos del evento con la sección visible
     */
    __onSectionVisible(data) {
        // Aquí se puede implementar carga lazy adicional si es necesario
        // Por ejemplo, cargar datos solo cuando la sección es visible
        // console.log('Sección visible:', data.section);
    }

    changePassword() {
        const view = new ChangePasswordModalView();

        this.App.trigger('show:modal', {
            title: 'Cambio de contraseña',
            view,
            options: {
                size: 'modal-md',
                centered: true,
                scrollable: false,
                autoFocus: '#claant',
                footer: [
                    {
                        text: 'Cancelar',
                        className: 'd-none',
                        onClick: (modalView, app) => {
                            app.trigger('hide:modal', modalView);
                        },
                    },
                    {
                        text: 'Cambiar clave',
                        className: 'btn-primary',
                        onClick: (modalView, app) => {
                            if (modalView && typeof modalView.submit === 'function') {
                                modalView.submit(app);
                            }
                        },
                    },
                ],
            },
        });
    }

    descargaDocumentos() {}

    __buscarServicios(transfer) {
        const { callback, silent } = transfer;
        this.App.trigger('syncro', {
            url: this.App.url('principal/servicios'),
            data: {},
            silent,
            callback: (response) => {
                if (response) {
                    if (response.success === true) {
                        _.extend(this.App.Collections, response.data);
                        return callback(response);
                    }
                }
                return callback(false);
            },
        });
    }

    __syncroSolicitudes() {
        this.App.trigger('syncro', {
            url: this.App.url('principal/actualiza_estado_solicitudes'),
            data: {},
            callback: (response) => {
                if (response.success) {
                    this.App.trigger('alert:success', { message: response.msj });
                }
            },
        });
    }

    __validaSyncro() {
        this.App.trigger('syncro', {
            url: this.App.url('principal/valida_syncro'),
            data: {},
            callback: (response) => {
                if (response.success) {
                    if (response.data.syncron) {
                        this.trigger('syncro:request');
                    }
                    $('#show_date_syncron').html(response.data.ultimo_syncron);
                }
            },
            silent: true,
        });
    }

    destroy() {
        this.stopListening();
        if (this.region && this.region instanceof Region) this.region.remove();
    }
}

export { ControllerPrincipal };
