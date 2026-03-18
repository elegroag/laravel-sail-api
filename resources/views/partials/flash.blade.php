<script type='text/template' id='tmp_bienvenida'>
    <div class='col-xs-12'>
        <h3 class='text-center' style='color:#78b64b'>Seguridad De La Información<br/>(<small>Protección De Datos</small>)</h3><br/>
        <p class='text-left'><%=mensaje%></p>
        <p class='text-left'>“Bienvenido a la Caja de Compensación Familiar del Caquetá COMFACA. LA SEGURIDAD Y PRIVACIDAD DE LOS DATOS E INFORMACIÓN ESTÁ EN SUS MANOS, por ello, recuerda en todo momento su compromiso frente a conocer y aplicar las políticas de seguridad de la información y protección de datos personales establecidas en la organización.</p><br/>
        <p class='text-left'>Con la finalidad de asegurar el debido el cumplimiento de normativas legales y directrices internas o externas, la Caja de Compensación Familiar del Caquetá COMFACA podrá monitorear, supervisar y vigilar en cualquier momento el cumplimiento y adecuada aplicación de las políticas, lineamientos y demás aspectos que hayan sido generados para salvaguardar la seguridad y privacidad de la información. Finalmente, recuerde que un incumplimiento de las políticas y demás lineamientos puede generar sanciones.”</p>
    </div>
</script>

<script type='text/javascript'>
    $(document).ready(function () {
        let _flash = @json(get_flashdata()) || null;
        if (!_flash || _flash.length === 0) return;
        setTimeout(() => {
            if (_flash.error && _flash.error.msj) {
                Swal.fire({
                    title: " Notificación Error",
                    text: _flash.error.msj,
                    icon: "warning",
                    showConfirmButton: false,
                    button: "Continuar"
                });
            }

            if (_flash.notify && _flash.notify.msj) {
                Swal.fire({
                    title: "Notificación",
                    text: _flash.notify.msj,
                    icon: "warning",
                    showConfirmButton: false,
                    timer: 10000
                });
            }

            console.log('flash', _flash);

            if (_flash.success && _flash.success.msj) {
                if (_flash.success.template) {
                    let tplEl = document.getElementById(_flash.success.template);
                    if (tplEl && typeof _ !== 'undefined' && _.template) {
                        let _content = _.template(tplEl.innerHTML);
                        Swal.fire({
                            html: _content({ mensaje: _flash.success.msj }),
                            icon: "success",
                            showConfirmButton: false,
                            timer: 50000
                        });
                    }
                } else if (_flash.success.type === 'html') {
                    Swal.fire({
                        title: "Proceso exitoso",
                        html: _flash.success.msj,
                        icon: "success",
                        confirmButtonText: "Continuar",
                        showCancelButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        timer: 20000
                    });
                } else {
                    Swal.fire({
                        title: "Proceso exitoso",
                        text: _flash.success.msj,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 20000
                    });
                }
            }
        }, 200);

    });
</script>