<div class='card-header'>
    <h5 class="text-primary text-center p-3 m-0">
        Iniciar sesión portal en línea
    </h5>
</div>
<div class="card-body px-lg-5 py-lg-3">
    <form id='formLogin'>
        <div class="form-group mb-2" group-for='tipafi'>
            <label class="form-control-label" for="tipafi">Tipo de afiliado</label>
            <span id='component_tipafi'></span>
        </div>

        <div class="form-group mb-1" group-for='coddoc'>
            <label class="form-control-label" for="coddoc">Tipo documento</label>
            <span id='component_coddoc'></span>
        </div>

        <div class="form-group mb-1" group-for='documento'>
            <label class="form-control-label" id='lb_documento' for="documento">Identificación</label>
            <div class="input-group input-group-alternative mb-4">
                <span class="input-group-text"><svg class="text-dark" width="16px" height="16px" viewBox="0 0 46 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <title>customer-support</title>
                        <g id="Basic-Elements" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Rounded-Icons" transform="translate(-1717.000000, -291.000000)" fill="#9e9b9bff" fill-rule="nonzero">
                                <g id="Icons-with-opacity" transform="translate(1716.000000, 291.000000)">
                                    <g id="customer-support" transform="translate(1.000000, 0.000000)">
                                        <path class="color-background" d="M45,0 L26,0 C25.447,0 25,0.447 25,1 L25,20 C25,20.379 25.214,20.725 25.553,20.895 C25.694,20.965 25.848,21 26,21 C26.212,21 26.424,20.933 26.6,20.8 L34.333,15 L45,15 C45.553,15 46,14.553 46,14 L46,1 C46,0.447 45.553,0 45,0 Z" id="Path" opacity="0.59858631"></path>
                                        <path class="color-foreground" d="M22.883,32.86 C20.761,32.012 17.324,31 13,31 C8.676,31 5.239,32.012 3.116,32.86 C1.224,33.619 0,35.438 0,37.494 L0,41 C0,41.553 0.447,42 1,42 L25,42 C25.553,42 26,41.553 26,41 L26,37.494 C26,35.438 24.776,33.619 22.883,32.86 Z" id="Path"></path>
                                        <path class="color-foreground" d="M13,28 C17.432,28 21,22.529 21,18 C21,13.589 17.411,10 13,10 C8.589,10 5,13.589 5,18 C5,22.529 8.568,28 13,28 Z" id="Path"></path>
                                    </g>
                                </g>
                            </g>
                        </g>
                    </svg>
                </span>
                <input class="form-control" id='documento' name="documento" placeholder="Identificación" type="text" autocomplete="off" />
            </div>
        </div>

        <div class="form-group mb-1 inbox-clave" group-for='clave'>
            <label class="form-control-label" for="clave">Clave</label>
            <div class="input-group input-group-alternative mb-4">
                <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                <input class="form-control" id='clave' name="clave" placeholder="*****" type="password" autocomplete="off" />
            </div>
        </div>

        <div class="form-group mt-3">
            <div class="text-center">
                <button type="button" id="bt_autenticate" class="btn btn-success my-4 mt-3 w-80">Iniciar sesión</button>
            </div>
        </div>
    </form>
    <br />
    <div class="col-xs-12">
        <div class="text-center">
            <a class="link mb-4 text-info" id='bt_recuperar_clave'>Pulsar si olvidó su clave aquí</a>
        </div>
        <div class="text-center inbox-clave">
            <a class="link mb-4 text-info" data-has='N' id='bt_solicitar_clave'>Pulsar cuando no tienes clave aquí</a>
        </div>
        <div class="text-center inbox-clave">
            <a class="link mb-4 text-info" data-has='N' id='bt_cambia_email'>Pulsar para cambiar su correo electrónico</a>
        </div>
        <div class="text-center inuse-clave" style='display:none'>
            <a class="link mb-4 text-info" data-has='S' id='bt_usar_clave'>Usar clave aquí</a>
        </div>
    </div>
</div>