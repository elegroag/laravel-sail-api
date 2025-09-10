<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta
        name="csrf-token"
        content="<?= $csrf_token ?>"
        controller="<?= Router::getController() ?>"
        path="<?= Core::getInstancePath() ?>"
        app="<?= Router::getActiveApplication() ?>"
        action="<?= Router::getAction() ?>" />
    <?php echo Tag::getDocumentTitle();    ?>
    <?php echo Tag::Assets('favicon', 'png') ?>
    <?php echo Tag::Assets('bootstrap/css/bootstrap.min', 'css') ?>
    <?php echo Tag::Assets('fonts/webfonts/opensans-bold/fonts', 'font') ?>
    <?php echo Tag::Assets('fonts/webfonts/opensans-regular/fonts', 'font') ?>
    <?php echo Tag::Assets('fonts/webfonts/roboto-bold/fonts', 'font') ?>
    <?php echo Tag::Assets('fonts/webfonts/roboto-regular/fonts', 'font') ?>
    <?php echo Tag::Assets('argon/argon', 'css') ?>
    <?php echo Tag::Assets('font_awesome/all', 'css') ?>
    <?php echo Tag::Assets('argon/nucleo', 'css') ?>
    <?php echo Tag::Assets('sweetalert2/dist/sweetalert2.min', 'css') ?>

    <?php echo Tag::stylesheetLink('style', false, strtotime('now')); ?>
    <?php echo Tag::stylesheetLinkTags(); ?>

    <?php echo Tag::Assets("manifest", 'json'); ?>
    <?php echo Tag::Assets("jquery/jquery.min", 'js'); ?>
    <?php echo Tag::Assets("plugins/js.cookie", 'js'); ?>
    <?php echo Tag::Assets("sweetalert2/dist/sweetalert2.all.min", 'js'); ?>
    <?php echo Tag::Assets("underscore/underscore-umd", 'js'); ?>
    <?php echo Tag::Assets("backbone/backbone-min", 'js'); ?>
    <?php echo Tag::Assets("bootstrap/js/popper.min", 'js'); ?>
    <?php echo Tag::Assets("bootstrap/js/bootstrap.min", 'js'); ?>
    <?php echo Tag::Assets("noty/noty", 'js'); ?>

    <script type='text/template' id='tmp_bienvenida'>
        <div class='col-xs-12'>
            <h3 class='text-center' style='color:#78b64b'>Seguridad De La Información<br/>(<small>Protección De Datos</small>)</h3><br/>
            <p class='text-left'><%=mensaje%></p>
            <p class='text-left'>“Bienvenido a la Caja de Compensación Familiar del Caquetá COMFACA. LA SEGURIDAD Y PRIVACIDAD DE LOS DATOS E INFORMACIÓN ESTÁ EN SUS MANOS, por ello, recuerda en todo momento su compromiso frente a conocer y aplicar las políticas de seguridad de la información y protección de datos personales establecidas en la organización.</p><br/>
            <p class='text-left'>Con la finalidad de asegurar el debido el cumplimiento de normativas legales y directrices internas o externas, la Caja de Compensación Familiar del Caquetá COMFACA podrá monitorear, supervisar y vigilar en cualquier momento el cumplimiento y adecuada aplicación de las políticas, lineamientos y demás aspectos que hayan sido generados para salvaguardar la seguridad y privacidad de la información. Finalmente, recuerde que un incumplimiento de las políticas y demás lineamientos puede generar sanciones.”</p>
        </div>
    </script>

    <script type='text/javascript'>
        window.onload = function() {
            <?php
            $flash = Flash::get_flashdata();
            if ($flash) { ?>
                var _flash = <?= json_encode($flash, true) ?>;
                <? if (isset($flash['error'])) { ?>
                    swal.fire({
                        "title": "Notificación Error",
                        "text": _flash.error.msj,
                        "icon": "warning",
                        "showConfirmButton": false,
                        "button": "Continuar"
                    });
                <? } ?>
                <? if (isset($flash['notify'])) { ?>
                    swal.fire({
                        "title": "Notificación",
                        "text": _flash.notify.msj,
                        "icon": "warning",
                        "showConfirmButton": false,
                        "timer": 10000
                    });
                <? } ?>
                <? if (isset($flash['success'])) {
                    if (isset($flash['success']['template'])) { ?>
                        var _content = _.template($("#<?= $flash['success']['template'] ?>").html());
                        swal.fire({
                            "html": _content({
                                "mensaje": _flash.success.msj
                            }),
                            "icon": "success",
                            "showConfirmButton": false,
                            "timer": 50000
                        });
                    <? } else { ?>
                        swal.fire({
                            "title": "Proceso exitoso",
                            "text": _flash.success.msj,
                            "icon": "success",
                            "showConfirmButton": false,
                            "timer": 20000
                        });
                <?  }
                }   ?>
            <? } ?>
        };
    </script>
</head>

<body class="bg-gradient-primary">
    <?= View::renderView("templates/loading"); ?>
    <?php
    Core::importHelper('assets');
    echo Tag::stylesheetLink('Cajas/login');
    ?>

    <div class="container login-subsido">
        <div class='row center-xs'>
            <div class='col-md-7 box-login'>
                <div class="logo mb-4 mt-4">
                    <?php echo Tag::image("Mercurio/logo-min.png", "class: img-logo", "style: width:90pt"); ?>
                </div>
                <div class='login-item'>
                    <div class='col-xs-12'>
                        <h3>COMFACA EN LÍNEA <br /><small>(Administrativo)</small></h3>
                        <h4>POLÍTICA TRATAMIENTO DE DATOS:</h4>
                        <p class='text-justify'>
                            COMFACA identificado con Nit 891.190.047-2 es responsable del tratamiento de datos personales de su población afiliada incluyendo trabajadores, beneficiarios y empleadores, y en tal virtud informamos que al ingresar al sistema SISUWEB, usted como funcionario debe velar por la seguridad y confidencialidad de los datos, recuerde que la información debe ser protegida de acuerdo con nuestras políticas de protección de datos personales conforme a la Ley 1581 del 2012 y el decreto 1074 del 2015. <br />Todos los reportes generados con su usuario quedaran registrados bajo el mismo y usted asume la responsabilidad de acuerdo con lo declarado anteriormente.<br />
                            Para más información de la política puede vistar nuestro sitio web <br /> <a class='link-text' href='https://comfaca.dataprotected.co' target="_blank">comfaca.dataprotected.co</a></p>

                        <p class='text-left'>
                            <label class='checkbox text-info'>
                                <input type='checkbox' id='comfirmar_politica' name='confirmar_politica'>
                                Confirme que ha leído el anterior mensaje de compromiso, confidencialidad y buen uso de la información.
                            </label>
                        </p>
                        <p>Si ya ha aceptado la política previamente puede omitir este paso.</p>
                    </div>
                </div>
            </div>

            <div class='col-md-4 box-login'>
                <div class='box'>
                    <div class="login-item">
                        <div class="logo">
                            <br /><br />
                            <h3 id='titulo_autenticacion_opcion'>Iniciar Sesíon</h3><br />
                            <p class='decripcion'>Los siguientes campos son requeridos para el proceso de autenticación.</p>
                        </div>
                        <?php echo Tag::form("login/autenticar", "id: form_autenticar"); ?>
                        <div class="form form-login">

                            <p class='error_user error'></p>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-merge input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-circle-08"></i></span>
                                    </div>
                                    <input class="form-control pl-1" id="user" name="user" placeholder="Usuario" type="text">
                                </div>
                            </div>

                            <p class='error_clave error'></p>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-merge input-group-alternative">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                                    </div>
                                    <input class="form-control pl-1" id="password" name="password" placeholder="Clave" type="password">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id='btnToggle'><i id='eyeIcon' class="fa fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group md-3">
                                <button type='button' class='btn btn-md btn-primary my-4 btn-submit btn-block' id='bt_autenticar'>Entrar</button>
                            </div>
                        </div>
                        <?php echo Tag::endForm(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4">
        <div class="container">
            <div class="row align-items-center justify-content-xl-between">
                <div class="col-xl-12">
                    <div class="copyright text-center text-muted text-white">
                        &copy; 2022 <a href="http://comfaca.com/master" class="font-weight-bold ml-1" target="_blank">COMFACA.COM</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?
    echo Tag::javascriptInclude('core/base-source');
    echo Tag::javascriptInclude('Cajas/login/build.login');
    echo Tag::Assets("argon/argon", 'js');
    echo Tag::getJavascriptLocation();
    echo Tag::javascriptSources();
    ?>
</body>

</html>