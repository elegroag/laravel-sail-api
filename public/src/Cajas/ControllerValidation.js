import { Region } from '@/Common/Region';
import ValidationService from '@/Componentes/Services/ValidationService';
import { LayoutCajasView } from '@/Componentes/Views/LayoutCajasView';
import loading from '@/Componentes/Views/Loading';

class ControllerValidation {
    validationService = null;
    App = null;

    __estados = {
        '': 'Pendientes',
        P: 'Pendientes',
        R: 'Rechazadas',
        X: 'Rechazadas',
        A: 'Activas',
        I: 'Inactivas',
        D: 'Devueltas',
        T: 'Temporales',
    };

    constructor(options = { region: null, layout: null }) {
        _.extend(this, options);
        _.extend(this, Backbone.Events);
        this.validationService = ValidationService;
    }

    initialize() {
        this.layout = new LayoutCajasView();
        if (this.region instanceof Region) {
            this.region.show(this.layout);
        } else {
            if (_.isString(this.region)) $(this.region).html(this.layout);
        }
        this.App.layout = this.layout;
        loading.hide();
    }

    __aplicarFiltro(transfer) {
        this.validationService.aplicarFiltro(this.App, transfer);
    }

    __cambiarPagina(transfer) {
        this.validationService.cambiarPagina(this.App, transfer);
    }

    __buscarPagina(transfer) {
        this.validationService.buscarPagina(this.App, transfer);
    }

    __devolverSolicitud(transfer) {
        this.validationService.devolverSolicitud(this.App, transfer);
    }

    __rechazaSolicitud(transfer) {
        this.validationService.rechazaSolicitud(this.App, transfer);
    }

    __aprobarSolicitud(transfer) {
        this.validationService.aprobarSolicitud(this.App, transfer);
    }

    __volverLista() {
        this.destroy();
        this.App.router.navigate('list', { trigger: true, replace: true });
    }

    __editarRequest(data) {
        this.destroy();
        this.App.router.navigate('edit/' + data.id, { trigger: true });
    }

    __aportesInformation(data) {
        this.destroy();
        this.App.router.navigate('aportes/' + data.id, { trigger: true });
    }

    __notificarRequest(data) {
        this.destroy();
        this.App.router.navigate('notificar/' + data.id, { trigger: true });
    }

    __infoRequest(data) {
        this.destroy();
        this.App.router.navigate('info/' + data.id, { trigger: true });
    }

    __showFiltro() {
        const myModal = new bootstrap.Modal('#filtrar-modal', {
            keyboard: false,
        });
        myModal.show();
    }

    __showReporte() {
        this.destroy();
        this.App.router.navigate('reportes', { trigger: true });
    }

    __deshacerSolicitud(transfer = {}) {
        this.validationService.deshacerSolicitud(this.App, transfer);
    }

    destroy() {
        this.stopListening();
        if (this.region && this.region instanceof Region) this.region.remove();
    }
}

export { ControllerValidation };
