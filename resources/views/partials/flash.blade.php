@php($flash = get_flashdata())
@if (!empty($flash))
    {{-- JSON seguro para pasar datos al JS sin directivas Blade dentro del <script> --}}
    <script type="application/json" id="flash-data">{!! json_encode($flash, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}</script>
    <script type='text/javascript'>
    // Lee el JSON embebido y muestra notificaciones con SweetAlert
    document.addEventListener('DOMContentLoaded', function () {
        var dataEl = document.getElementById('flash-data');
        if (!dataEl) return;
        try { var _flash = JSON.parse(dataEl.textContent || '{}'); } catch (e) { return; }

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

        if (_flash.success && _flash.success.msj) {
            if (_flash.success.template) {
                var tplEl = document.getElementById(_flash.success.template);
                if (tplEl && typeof _ !== 'undefined' && _.template) {
                    var _content = _.template(tplEl.innerHTML);
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
    });
    </script>
@endif
