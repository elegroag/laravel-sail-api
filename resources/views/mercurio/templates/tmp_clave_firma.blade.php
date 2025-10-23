@include("partials.modal_generic", [
    "hideHeader" => true,
    "hideFooter" => true,
    "idModal" => 'modalClaveFirma'
])

<script type="text/template" id='tmp_clave_firma'>
    <div class="modal-header">
        <h5 class="modal-title" id="modalClaveFirmaLabel">Configurar clave para Firma Digital</h5>
    </div>
    <div class="modal-body">
        <p class="mb-3 text-justify">Para crear su firma digital es necesario definir una clave de 6 dígitos. Esta clave se usará para proteger su llave privada. Procura guardarla en un lugar seguro la clave de firma digital, ya que no podrá ser recuperada.<br/>
        </p>
        <div class="mb-3">
            <ul class="list">
                <li class="text-muted">Si olvida su clave de firma digital, no podrá firmar documentos electrónicos.</li>
                <li class="text-muted">Al no tener su clave de firma digital, no podrá realizar solicitudes de afiliación.</li>
            </ul>
        </div>
        <form id="formClaveFirma" autocomplete="off">
          <div class="mb-3">
            <label class="form-label w-100 text-center">Clave (6 dígitos)</label>
            <div class="d-flex justify-content-center align-items-center gap-2">
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 1" />
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 2" />
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 3" />
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 4" />
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 5" />
              <input class="form-control text-center digit-input" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="1" aria-label="Dígito 6" />
            </div>
            <div class="form-text text-center mt-2">Debe contener exactamente 6 números.</div>
          </div>
        </form>
        <div class="alert alert-danger d-none" id="alertClaveFirma"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btnGuardarClaveFirma">Guardar clave</button>
      </div>
</script>

<style>
    .digit-input {
        font-size: 18px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin: 0 5px;
        outline: none;
        transition: border-color 0.2s;
        width: 3rem;
        font-size: 1.25rem;
    }
    .digit-input:focus {
        border-color: #86c1ff;
        box-shadow: 0 0 0 0.2rem rgba(134, 193, 255, 0.1);
    }
</style>