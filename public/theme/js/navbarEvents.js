// navbarEvents.js
// Eventos del navbar (Accesos rápidos): abre modales para cambiar email y contraseña.
// Se implementa con jQuery + $('#modal_generic').modal() para compatibilidad.

(function () {
    function getPasswordStrength(password) {
        const value = String(password || '');

        let score = 0;
        if (value.length >= 6) score += 1;
        if (value.length >= 10) score += 1;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score += 1;
        if (/\d/.test(value)) score += 1;
        if (/[^a-zA-Z0-9]/.test(value)) score += 1;

        const max = 5;
        const percent = Math.round((score / max) * 100);

        let label = 'Muy débil';
        let className = 'bg-danger';
        if (score >= 2) {
            label = 'Débil';
            className = 'bg-warning';
        }
        if (score >= 3) {
            label = 'Media';
            className = 'bg-info';
        }
        if (score >= 4) {
            label = 'Fuerte';
            className = 'bg-success';
        }

        return { score, percent, label, className };
    }

    function updatePasswordStrengthUI(password) {
        const res = getPasswordStrength(password);
        const $bar = $('#navbar_password_strength_bar');
        const $label = $('#navbar_password_strength_label');
        if (!$bar.length || !$label.length) return;

        $bar.removeClass('bg-danger bg-warning bg-info bg-success')
            .addClass(res.className)
            .css('width', `${res.percent}%`)
            .attr('aria-valuenow', res.percent);
        $label.text(res.label);
    }

    function setFieldInvalid($input, message) {
        $input.addClass('is-invalid');
        const id = $input.attr('id');
        if (id) {
            const $feedback = $('#' + id + '_error');
            if ($feedback.length) $feedback.text(message || 'Campo inválido');
        }
    }

    function clearFieldInvalid($input) {
        $input.removeClass('is-invalid');
        const id = $input.attr('id');
        if (id) {
            const $feedback = $('#' + id + '_error');
            if ($feedback.length) $feedback.text('');
        }
    }

    function setButtonLoading($btn, loading) {
        if (!$btn || !$btn.length) return;

        if (loading) {
            if (!$btn.data('original-html')) {
                $btn.data('original-html', $btn.html());
            }
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + '<span>Procesando...</span>');
            return;
        }

        $btn.prop('disabled', false);
        const original = $btn.data('original-html');
        if (original) $btn.html(original);
    }

    function isValidEmail(value) {
        if (!value) return false;
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(value).toLowerCase());
    }

    /**
     * Obtiene el CSRF token desde el meta tag.
     */
    function getCsrfToken() {
        const el = document.querySelector("[name='csrf-token']");
        return el ? el.getAttribute('content') : '';
    }

    /**
     * Configura el tamaño del modal genérico.
     */
    function setModalSize(sizeClass) {
        const $dialog = $('#size_modal_generic');
        $dialog.removeClass('modal-sm modal-md modal-lg modal-xl');
        if (sizeClass) $dialog.addClass(sizeClass);
    }

    /**
     * Renderiza el contenido dentro del modal genérico.
     */
    function renderInGenericModal(html, sizeClass) {
        setModalSize(sizeClass);
        $('#show_modal_generic').html(html);
        $('#modal_generic').modal('show');
    }

    /**
     * Helper para hacer POST JSON/FORM con headers CSRF.
     */
    function postForm(url, data) {
        const csrf = getCsrfToken();
        return $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                if (csrf) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                    xhr.setRequestHeader('Authorization', 'Bearer ' + csrf);
                }
            },
        });
    }

    /**
     * Formulario modal para cambio de email (requiere clave actual).
     */
    function openCambioEmailModal(url) {
        const html = `
            <div class="card border-0 shadow-sm mb-0">
                <div class="card-header bg-white p-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center rounded-circle bg-gradient-warning text-white" style="width:44px;height:44px;">
                            <i class="ni ni-email-83"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Actualizar email de notificación</h5>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Por seguridad confirma con tu clave actual.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <form id="navbar_cambio_email_form" autocomplete="off" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="navbar_email" class="form-control-label">Nuevo email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                                    <input type="email" id="navbar_email" name="email" class="form-control" placeholder="correo@ejemplo.com" autocomplete="email" />
                                </div>
                                <div class="invalid-feedback" id="navbar_email_error"></div>
                                <div class="form-text">Usaremos este email para notificaciones y confirmaciones.</div>
                            </div>

                            <div class="col-12">
                                <label for="navbar_email_claant" class="form-control-label">Confirmar con tu clave actual</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    <input type="password" id="navbar_email_claant" name="claant" class="form-control" placeholder="Clave actual" autocomplete="current-password" />
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="toggle-password" data-target="#navbar_email_claant" aria-label="Mostrar u ocultar clave">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="navbar_email_claant_error"></div>
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                                <button type="button" class="btn btn-light" data-dismiss="modal" id="navbar_cambio_email_cancel">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="navbar_cambio_email_submit">Guardar cambios</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        `;

        renderInGenericModal(html, 'modal-lg');

        $('#navbar_cambio_email_cancel')
            .off('click')
            .on('click', function (e) {
                e.preventDefault();
                $('#modal_generic').modal('hide');
            });

        $('#navbar_cambio_email_form')
            .off('submit')
            .on('submit', function (e) {
                e.preventDefault();

                const $email = $('#navbar_email');
                const $claant = $('#navbar_email_claant');
                const $btn = $('#navbar_cambio_email_submit');

                clearFieldInvalid($email);
                clearFieldInvalid($claant);

                const email = $email.val();
                const claant = $claant.val();

                let valid = true;
                if (!isValidEmail(email)) {
                    setFieldInvalid($email, 'Ingresa un email válido.');
                    valid = false;
                }
                if (!claant) {
                    setFieldInvalid($claant, 'Debes confirmar con tu clave actual.');
                    valid = false;
                }
                if (!valid) return;

                Swal.fire({
                    title: 'Confirmar cambio',
                    text: '¿Confirmas el cambio del email de notificación?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    setButtonLoading($btn, true);
                    $email.prop('disabled', true);
                    $claant.prop('disabled', true);

                    postForm(url, { email, claant })
                        .done((response) => {
                            setButtonLoading($btn, false);
                            $email.prop('disabled', false);
                            $claant.prop('disabled', false);

                            if (response && response.success) {
                                Swal.fire('Proceso completado', response.msj || 'Email actualizado correctamente.', 'success');
                                $('#modal_generic').modal('hide');
                                return;
                            }
                            Swal.fire('No fue posible', (response && response.msj) || 'No fue posible cambiar el email.', 'error');
                        })
                        .fail((xhr) => {
                            setButtonLoading($btn, false);
                            $email.prop('disabled', false);
                            $claant.prop('disabled', false);
                            Swal.fire('Error', xhr.responseText || 'Error inesperado al cambiar el email.', 'error');
                        });
                });
            });

        setTimeout(() => $('#navbar_email').trigger('focus'), 200);
    }

    /**
     * Formulario modal para cambio de clave.
     */
    function openCambioClaveModal(url) {
        const html = `
            <div class="card border-0 shadow-sm mb-0">
                <div class="card-header bg-white p-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center rounded-circle bg-gradient-info text-white" style="width:44px;height:44px;">
                            <i class="ni ni-key-25"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Cambiar contraseña</h5>
                            <p class="text-muted mb-0" style="font-size: 0.9rem;">Te pediremos tu clave actual para autorizar el cambio.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <form id="navbar_cambio_clave_form" autocomplete="off" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="navbar_claant" class="form-control-label">Clave actual</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    <input type="password" id="navbar_claant" name="claant" class="form-control" placeholder="Clave actual" autocomplete="current-password" />
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="toggle-password" data-target="#navbar_claant" aria-label="Mostrar u ocultar clave">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="navbar_claant_error"></div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="navbar_clave" class="form-control-label">Nueva contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ni ni-key-25"></i></span>
                                    <input type="password" id="navbar_clave" name="clave" class="form-control" placeholder="Nueva contraseña" autocomplete="new-password" />
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="toggle-password" data-target="#navbar_clave" aria-label="Mostrar u ocultar clave">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="navbar_clave_error"></div>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Fuerza</small>
                                        <small class="text-muted" id="navbar_password_strength_label">Muy débil</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div
                                            class="progress-bar bg-danger"
                                            id="navbar_password_strength_bar"
                                            role="progressbar"
                                            style="width: 0%"
                                            aria-valuenow="0"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                    <div class="form-text">Recomendación: mínimo 6 caracteres, combina mayúsculas, números y símbolos.</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="navbar_clacon" class="form-control-label">Confirmación</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ni ni-check-bold"></i></span>
                                    <input type="password" id="navbar_clacon" name="clacon" class="form-control" placeholder="Repite la nueva contraseña" autocomplete="new-password" />
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="toggle-password" data-target="#navbar_clacon" aria-label="Mostrar u ocultar clave">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="navbar_clacon_error"></div>
                            </div>

                            <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                                <button type="button" class="btn btn-light" data-dismiss="modal" id="navbar_cambio_clave_cancel">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="navbar_cambio_clave_submit">Guardar contraseña</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        `;

        renderInGenericModal(html, 'modal-xl');

        $('#navbar_cambio_clave_cancel')
            .off('click')
            .on('click', function (e) {
                e.preventDefault();
                $('#modal_generic').modal('hide');
            });

        $('#navbar_cambio_clave_form')
            .off('submit')
            .on('submit', function (e) {
                e.preventDefault();

                const $claant = $('#navbar_claant');
                const $clave = $('#navbar_clave');
                const $clacon = $('#navbar_clacon');
                const $btn = $('#navbar_cambio_clave_submit');

                clearFieldInvalid($claant);
                clearFieldInvalid($clave);
                clearFieldInvalid($clacon);

                const claant = $claant.val();
                const clave = $clave.val();
                const clacon = $clacon.val();

                let valid = true;
                if (!claant) {
                    setFieldInvalid($claant, 'Debes ingresar tu clave actual.');
                    valid = false;
                }
                if (!clave || String(clave).length < 6) {
                    setFieldInvalid($clave, 'La nueva contraseña debe tener mínimo 6 caracteres.');
                    valid = false;
                }
                if (!clacon) {
                    setFieldInvalid($clacon, 'Confirma la nueva contraseña.');
                    valid = false;
                } else if (clave !== clacon) {
                    setFieldInvalid($clacon, 'La confirmación no coincide.');
                    valid = false;
                }
                if (!valid) return;

                Swal.fire({
                    title: 'Confirmar cambio',
                    text: '¿Confirmas el cambio de contraseña?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cambiar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    setButtonLoading($btn, true);
                    $claant.prop('disabled', true);
                    $clave.prop('disabled', true);
                    $clacon.prop('disabled', true);

                    postForm(url, { claant, clave, clacon })
                        .done((response) => {
                            setButtonLoading($btn, false);
                            $claant.prop('disabled', false);
                            $clave.prop('disabled', false);
                            $clacon.prop('disabled', false);

                            if (response && response.success) {
                                Swal.fire('Proceso completado', response.msj || 'Contraseña actualizada correctamente.', 'success');
                                $('#modal_generic').modal('hide');
                                return;
                            }
                            Swal.fire('No fue posible', (response && response.msj) || 'No fue posible cambiar la contraseña.', 'error');
                        })
                        .fail((xhr) => {
                            setButtonLoading($btn, false);
                            $claant.prop('disabled', false);
                            $clave.prop('disabled', false);
                            $clacon.prop('disabled', false);
                            Swal.fire('Error', xhr.responseText || 'Error inesperado al cambiar la contraseña.', 'error');
                        });
                });
            });

        setTimeout(() => $('#navbar_claant').trigger('focus'), 200);
        setTimeout(() => updatePasswordStrengthUI($('#navbar_clave').val()), 250);
    }

    /**
     * Inicialización de eventos del navbar.
     */
    $(function () {
        // Click: abrir modal de cambio de email
        $(document).on('click', '[data-toggle="navbar-change-email"]', function (e) {
            e.preventDefault();
            const url = $(this).attr('data-url');
            if (!url) return;
            openCambioEmailModal(url);
        });

        // Click: abrir modal de cambio de clave
        $(document).on('click', '[data-toggle="navbar-change-clave"]', function (e) {
            e.preventDefault();
            const url = $(this).attr('data-url');
            if (!url) return;
            openCambioClaveModal(url);
        });

        // Compatibilidad: algunos themes usan data-dismiss, otros data-bs-dismiss
        // Forzamos cierre del modal genérico si el usuario hace click en cualquier botón con data-dismiss="modal".
        $(document).on('click', '#modal_generic [data-dismiss="modal"]', function (e) {
            e.preventDefault();
            $('#modal_generic').modal('hide');
        });

        $(document).on('click', '#modal_generic [data-toggle="toggle-password"]', function (e) {
            e.preventDefault();
            const target = $(this).attr('data-target');
            if (!target) return;
            const $input = $(target);
            if (!$input.length) return;

            const current = $input.attr('type');
            $input.attr('type', current === 'password' ? 'text' : 'password');
            const $icon = $(this).find('i');
            if ($icon.length) {
                $icon.toggleClass('fa-eye fa-eye-slash');
            }
        });

        $(document).on('input', '#modal_generic input', function () {
            clearFieldInvalid($(this));
        });

        $(document).on('input', '#navbar_clave', function () {
            updatePasswordStrengthUI($(this).val());
        });
    });
})();
