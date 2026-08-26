$(() => {

     function reporte_excel_carga_laboral() {
        window.location.href = Utils.getKumbiaURL(
            $Kumbia.controller + '/reporte_excel_carga_laboral',
        );
    }

    function reporte_excel_indicadores() {
        var validator = $('#form').validate({
            rules: {
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        window.location.href = Utils.getKumbiaURL(
            $Kumbia.controller +
            '/reporte_excel_indicadores/' +
            $('#fecini').val() +
            '/' +
            $('#fecfin').val(),
        );
    }

    function consulta_indicadores() {
        var validator = $('#form').validate({
            rules: {
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_indicadores'),
                data: {
                    fecini: $('#fecini').val(),
                    fecfin: $('#fecfin').val(),
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#consulta').html(response);
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function reporte_auditoria() {
        var validator = $('#form').validate({
            rules: {
                tipopc: {
                    required: true
                },
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $('#form').submit();
    }

    function consulta_auditoria() {
        var validator = $('#form').validate({
            rules: {
                tipopc: {
                    required: true
                },
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_auditoria'),
                data: {
                    tipopc: $('#tipopc').val(),
                    fecini: $('#fecini').val(),
                    fecfin: $('#fecfin').val(),
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#consulta').html(response);
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function info(tipopc, id) {
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/info'),
                data: {
                    tipopc: tipopc,
                    id: id,
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#result_info').html(response);
                $('#capture-modal-info').modal();
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function consulta_activacion_masiva() {
        var validator = $('#form').validate({
            rules: {
                nit: {
                    required: true
                },
                fecini: {
                    required: true
                },
                fecfin: {
                    required: true
                },
            },
        });
        if (!$('#form').valid()) {
            return;
        }
        $.ajax({
                type: 'POST',
                url: Utils.getKumbiaURL($Kumbia.controller + '/consulta_activacion_masiva'),
                data: {
                    nit: $('#nit').val(),
                    fecini: $('#fecini').val(),
                    fecfin: $('#fecfin').val(),
                },
            })
            .done(function(transport) {
                var response = transport;
                $('#consulta').html(response);
            })
            .fail(function(jqXHR, textStatus) {
                alert('Request failed: ' + textStatus);
            });
    }

    function descarga_activacion(element) {
        window.open(Utils.getURL('temp/' + element.innerHTML));
    }

    const MODAL_ID = 'modal_generic';
    const MODAL_DIALOG_ID = 'size_modal_generic';
    const MODAL_CONTENT_ID = 'show_modal_generic';
    const CERTIFICADO_IFRAME_NAME = 'certificado_modal_iframe';
    let certificadoBlobUrl = null;

    function revokeCertificadoBlobUrl() {
        if (certificadoBlobUrl) {
            URL.revokeObjectURL(certificadoBlobUrl);
            certificadoBlobUrl = null;
        }
    }

    function destroySelect2Cedtra() {
        const $cedtra = $('#cedtra');
        if ($cedtra.length && $cedtra.hasClass('select2-hidden-accessible')) {
            $cedtra.select2('destroy');
        }
    }

    function initSelect2Cedtra(placeholder) {
        const $cedtra = $('#cedtra');
        if (!$cedtra.length || typeof $.fn.select2 !== 'function') {
            return;
        }

        destroySelect2Cedtra();
        $cedtra.select2({
            placeholder: placeholder || 'Seleccione trabajador',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0,
            dropdownParent: $cedtra.parent(),
        });
    }

    function resetTrabajadorSelect(placeholder) {
        destroySelect2Cedtra();
        $('#cedtra').html(`<option value="">${placeholder}</option>`);
        initSelect2Cedtra(placeholder);
    }

    function cargarTrabajadoresSelect(trabajadores) {
        destroySelect2Cedtra();

        const $cedtra = $('#cedtra');
        let html = '<option value=""></option>';

        Object.entries(trabajadores).forEach(([cedula, nombre]) => {
            const label = nombre && nombre !== cedula ? `${cedula} - ${nombre}` : cedula;
            html += `<option value="${cedula}">${label}</option>`;
        });

        $cedtra.html(html);
        initSelect2Cedtra('Buscar trabajador...');
    }

    function showCertificadoModal() {
        const el = document.getElementById(MODAL_ID);
        if (!el) {
            return;
        }

        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
            return;
        }

        if (typeof window.$ !== 'undefined' && typeof window.$(el).modal === 'function') {
            window.$(el).modal('show');
        }
    }

    function openCertificadoModal(title) {
        const $dialog = $(`#${MODAL_DIALOG_ID}`);
        if ($dialog.length) {
            $dialog.removeClass().addClass('modal-dialog modal-xl modal-dialog-scrollable');
        }

        const html = `
            <div class="card-header py-2">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">${title}</h5>
                    <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal" data-dismiss="modal"></button>
                </div>
            </div>
            <div class="card-body p-0">
                <iframe
                    id="${CERTIFICADO_IFRAME_NAME}"
                    name="${CERTIFICADO_IFRAME_NAME}"
                    style="width: 100%; height: 80vh; border: 0;"
                ></iframe>
            </div>
        `;

        $(`#${MODAL_CONTENT_ID}`).html(html);
        showCertificadoModal();
    }

    function cleanupCertificadoModal() {
        revokeCertificadoBlobUrl();

        const iframeEl = document.getElementById(CERTIFICADO_IFRAME_NAME);
        if (iframeEl) {
            iframeEl.setAttribute('src', 'about:blank');
        }
    }

    async function submitCertificadoTrabajador() {
        const $form = $('#form');
        if (!$form.length || !window.ConsultaCertificado?.urlTrabajadores) {
            return;
        }

        if (!$form.valid()) {
            return;
        }

        const formEl = $form.get(0);
        if (!formEl) {
            return;
        }

        const $button = $('#bt_certificado_afiliacion');
        $button.prop('disabled', true);

        try {
            const response = await fetch(formEl.action, {
                method: 'POST',
                body: new FormData(formEl),
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/pdf',
                },
            });

            if (!response.ok) {
                let msj = `No se pudo generar el certificado (${response.status})`;
                try {
                    const payload = await response.json();
                    if (payload?.msj) {
                        msj = payload.msj;
                    }
                } catch {
                    // La respuesta de error no es JSON.
                }
                throw new Error(msj);
            }

            const contentType = response.headers.get('Content-Type') || '';
            if (!contentType.includes('application/pdf')) {
                throw new Error('La respuesta del servidor no es un PDF válido.');
            }

            revokeCertificadoBlobUrl();
            const blob = await response.blob();
            certificadoBlobUrl = URL.createObjectURL(blob);

            openCertificadoModal('Certificado / Oficio');

            const iframeEl = document.getElementById(CERTIFICADO_IFRAME_NAME);
            if (iframeEl) {
                iframeEl.src = certificadoBlobUrl;
            }
        } catch (error) {
            const message = error instanceof Error ? error.message : 'Error al generar el certificado';
            alert(message);
        } finally {
            $button.prop('disabled', false);
        }
    }

    function cargarTrabajadoresPorNit() {
        const nit = $('#nit').val()?.trim();
        const url = window.ConsultaCertificado?.urlTrabajadores;

        if (!url) {
            return;
        }

        if (!nit) {
            resetTrabajadorSelect('— Ingrese NIT —');
            return;
        }

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                nit,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
        })
            .done(function (response) {
                if (!response?.success) {
                    alert(response?.msj || 'Error al cargar trabajadores');
                    resetTrabajadorSelect('— Sin trabajadores —');
                    return;
                }

                const trabajadores = response.trabajadores || {};
                const entries = Object.entries(trabajadores);

                if (!entries.length) {
                    resetTrabajadorSelect('— Sin trabajadores —');
                    return;
                }

                cargarTrabajadoresSelect(trabajadores);
            })
            .fail(function (jqXHR) {
                const msj = jqXHR.responseJSON?.msj || 'No se pudo cargar trabajadores';
                alert(msj);
                resetTrabajadorSelect('— Sin trabajadores —');
            });
    }

    if (window.ConsultaCertificado?.urlTrabajadores) {
        resetTrabajadorSelect('— Ingrese NIT primero —');

        $('#form').validate({
            rules: {
                nit: { required: true },
                cedtra: { required: true },
                tipo: { required: true },
            },
            highlight(element, errorClass, validClass) {
                const $elem = $(element);
                if ($elem.hasClass('select2-hidden-accessible')) {
                    $('#select2-' + $elem.attr('id') + '-container')
                        .parent()
                        .addClass(errorClass)
                        .removeClass(validClass);
                    return;
                }

                $elem.addClass(errorClass).removeClass(validClass);
            },
            unhighlight(element, errorClass, validClass) {
                const $elem = $(element);
                if ($elem.hasClass('select2-hidden-accessible')) {
                    $('#select2-' + $elem.attr('id') + '-container')
                        .parent()
                        .removeClass(errorClass)
                        .addClass(validClass);
                    return;
                }

                $elem.removeClass(errorClass).addClass(validClass);
            },
            errorPlacement(error, element) {
                const $elem = $(element);
                if ($elem.hasClass('select2-hidden-accessible')) {
                    error.insertAfter($('#select2-' + $elem.attr('id') + '-container').parent());
                    return;
                }

                error.insertAfter(element);
            },
        });

        $(document).on('blur change', '#nit', cargarTrabajadoresPorNit);
        $(document).on('click', '#bt_certificado_afiliacion', submitCertificadoTrabajador);
        $(document).off('hidden.bs.modal', `#${MODAL_ID}`, cleanupCertificadoModal);
        $(document).on('hidden.bs.modal', `#${MODAL_ID}`, cleanupCertificadoModal);
    }

});
