<div class="d-flex justify-content-center">
    <div class="card">
        <div class='card-header'>
            <h4 class="text-center p-4">Verificación PIN - Email</h4>
            <center>
            <img src="{{ asset('img/Mercurio/verification.jpg') }}" class="img-responsive" style="width:200px" />    
            </center> <br />
            <p class="text-justify mt-3">
                <span>Ingrese el código de verificación de 4 dígitos que le enviamos por correo electrónico.</span><br />
                <span>Queremos asegurarnos de que sea usted antes de ingresar a la plataforma "Comfaca En Línea".</span>
            </p>
        </div>
        <div class="card-body bg-white">
            <div class="row justify-content-center mt-3 mb-4">
                <div class="col-auto" id="form">
                    <input class='in-put-code' name='code_1' data-toggle='code' type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input class='in-put-code' name='code_2' data-toggle='code' type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input class='in-put-code' name='code_3' data-toggle='code' type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                    <input class='in-put-code' name='code_4' data-toggle='code' type="text" maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-center">
                <button class="btn btn-primary btn-embossed w-80" id='btnVerify'><i class="fas fa-hand-point-up"></i> Verificar aquí</button>
            </div>
        </div>
    </div>
</div>

<form id="formVerify" action="#" method="POST">
    @csrf
    <input type="hidden" name="dataVerify" id="dataVerify" />
</form>