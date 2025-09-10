<div class="card">
    <div class="card-body">
        <p class="m-2 text-center">Reportes de Solicitudes, por tipo de solicitud, en sus diferentes estados</p>

        <form id="form_reportesol">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="form-group">
                        <label for="tipo_solicitud">Tipo de Solicitud</label>
                        <?= Tag::selectStatic('tipo_solicitud', $tipo_solicitudes, 'class: form-control') ?>
                    </div>

                    <div class='form-group'>
                        <label for="estado_solicitud">Estado de Solicitud</label>
                        <select name="estado_solicitud" id="estado_solicitud" class="form-control">
                            <option value="">TODOS</option>
                            <option value="A">Activos</option>
                            <option value="D">Devueltos</option>
                            <option value="R">Rechazados</option>
                            <option value="C">Cancelados</option>
                            <option value="I">Inactivos</option>
                        </select>
                    </div>

                    <div class='form-group'>
                        <label for="fecha_solicitud">Fecha de envío</label>
                        <?= TagUser::calendar('fecha_solicitud', 'class: form-control', 'type: date') ?>
                    </div>

                    <div class='form-group'>
                        <label for="fecha_solicitud">Fecha de aprobación</label>
                        <?= TagUser::calendar('fecha_aprueba', 'class: form-control', 'type: date') ?>
                    </div>

                    <div class='form-group text-center mt-2'>
                        <button type="button" id="btn_generar_reporte" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    function downLoadFile(transfer) {
        const {
            url,
            filename
        } = transfer;
        const link = document.createElement('a');
        link.href = Utils.getKumbiaURL(url + '/' + filename);
        link.download = filename;
        console.log(link);
        link.click();
    }

    $(document).ready(function() {

        $("input[date='date']").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        $(document).on('click', '#btn_generar_reporte', function(e) {
            e.preventDefault();
            var tipo_solicitud = $('#tipo_solicitud').val();
            var estado_solicitud = $('#estado_solicitud').val();
            var fecha_solicitud = $('#fecha_solicitud').val();
            var fecha_aprueba = $('#fecha_aprueba').val();

            $.ajax({
                url: Utils.getKumbiaURL('reportesol/procesar'),
                type: 'POST',
                data: {
                    tipo: tipo_solicitud,
                    estado: estado_solicitud,
                    fecha_solicitud: fecha_solicitud,
                    fecha_aprueba: fecha_aprueba
                },
                success: function(response) {
                    downLoadFile(response);
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });
    });
</script>