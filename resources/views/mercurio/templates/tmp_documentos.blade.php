<div class="row">
    <div class="col-8">
        <fieldset class="mt-0 pt-0">
            <legend>Adjuntar los documentos</legend>
            <div class="row col-auto">
                <table class='table table-bordered  align-content-between mb-2' id='addArchivoRequeridos'>
                    <thead>
                        <tr>
                            <td>Adjuntos</td>
                            <td colspan='2'>Opciones</td>
                        </tr>
                    </thead>
                </table>
            </div>
        </fieldset>
    </div>

    <div class="col-4">
        <fieldset class="mt-0 pt-0">
            <legend>Nota</legend>
            <div class="row col-auto">
                <p class="p-1 text-gray">El adjunto que posee firma digital no se puede cambiar o borrar.
                    Para modificar la información del mismo, puede editar la "Ficha Principal De Registro". El sistema realiza la tarea de borrar el documento existente y crear uno nuevo con la respectiva firma.</p>
                    <img src="{{ asset('img/Mercurio/firma_digital.png') }}" class="img-responsive" style="width:200px" />
                    <p class="p-1 text-danger fw-bold mt-2">A partir de ahora, solo se admiten documentos en formato PDF. No se aceptan imágenes (JPG, PNG, etc.).</p>
            </div>
        </fieldset>
    </div>
</div>
