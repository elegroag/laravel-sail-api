import { $App } from '../App';
import { Region } from '../Common/Region';
import CuotaMonetariaView from '../Componentes/Views/CuotaMonetariaView';
import loading from '../Componentes/Views/Loading';
import NoGiroView from '../Componentes/Views/NoGiroView';
import PlanillaTrabajadorView from '../Componentes/Views/PlanillaTrabajadorView';

function ConsultaGiro() {
    $.ajax({
        type: 'POST',
        url: $App.url('subsidioemp/consulta_giro'),
        data: {
            perini: $('#perini').val(),
            perfin: $('#perfin').val(),
        },
        beforeSend: function (xhr) {
            loading.show();
            const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            if (csrf.length > 0) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
            }
        },
    })
        .done(function (response) {
            loading.hide();
            if (response.success == false) {
                $App.alert('error', {
                    title: 'Error',
                    message: response.msj,
                    timer: 8200,
                });
            } else {
                const view = new CuotaMonetariaView({ model: { cuotas: response.data } });
                const region = new Region({ el: '#consulta' });
                region.show(view);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $App.alert('error', {
                title: 'Error',
                message: textStatus.error,
                timer: 8200,
            });
        });
}

function ConsultaNoGiro() {
    $.ajax({
        type: 'POST',
        url: $App.url('subsidioemp/consulta_no_giro'),
        data: {
            perini: $('#perini').val(),
            perfin: $('#perfin').val(),
        },
        beforeSend: function (xhr) {
            loading.show();
            const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            if (csrf.length > 0) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
            }
        },
    })
        .done(function (response) {
            loading.hide();
            if (response.success == false) {
                $App.alert('error', {
                    title: 'Error',
                    message: response.msj,
                    timer: 8200,
                });
            } else {
                const view = new NoGiroView({ model: { motivos: response.data } });
                const region = new Region({ el: '#consulta' });
                region.show(view);
            }
        })
        .fail(function (jqXHR, textStatus) {
            $App.alert('error', {
                title: 'Error',
                message: textStatus.error,
                timer: 8200,
            });
        });
}

function ConsultaPlanillaTrabajador() {
    $.ajax({
        type: 'POST',
        url: $App.url('subsidioemp/consulta_planilla_trabajador'),
        data: {
            perini: $('#perini').val(),
            perfin: $('#perfin').val(),
        },
        beforeSend: function (xhr) {
            loading.show();
            const csrf = document.querySelector("[name='csrf-token']").getAttribute('content');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            if (csrf.length > 0) {
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
            }
        },
    })
        .done(function (response) {
            loading.hide();
            if (response.success == false) {
                $App.alert('error', {
                    title: 'Error',
                    message: response.msj,
                    timer: 8200,
                });
            } else {
                const view = new PlanillaTrabajadorView({ model: { planilla: response.data } });
                const region = new Region({ el: '#consulta' });
                region.show(view);
            }
        })
        .fail(function (jqXHR, textStatus) {
            alert('Request failed: ' + textStatus);
        });
}

function CertificadoAfiliacion() {
    validator = $('#form').validate({
        rules: {
            tipo: { required: true },
        },
    });
    if (!$('#form').valid()) {
        return;
    }
    $('#form').submit();
}

export { CertificadoAfiliacion, ConsultaGiro, ConsultaNoGiro, ConsultaPlanillaTrabajador };
