@extends('layouts.auth')

@section('application', 'cajas')

@push('scripts')
<script type='text/template' id='tmp_bienvenida'>
    <div class='col-xs-12'>
        <h3 class='text-center' style='color:#78b64b'>Seguridad De La Información<br/>(<small>Protección De Datos</small>)</h3><br/>
        <p class='text-left'><%=mensaje%></p>
        <p class='text-left'>“Bienvenido a la Caja de Compensación Familiar del Caquetá COMFACA. LA SEGURIDAD Y PRIVACIDAD DE LOS DATOS E INFORMACIÓN ESTÁ EN SUS MANOS, por ello, recuerda en todo momento su compromiso frente a conocer y aplicar las políticas de seguridad de la información y protección de datos personales establecidas en la organización.</p><br/>
        <p class='text-left'>Con la finalidad de asegurar el debido el cumplimiento de normativas legales y directrices internas o externas, la Caja de Compensación Familiar del Caquetá COMFACA podrá monitorear, supervisar y vigilar en cualquier momento el cumplimiento y adecuada aplicación de las políticas, lineamientos y demás aspectos que hayan sido generados para salvaguardar la seguridad y privacidad de la información. Finalmente, recuerde que un incumplimiento de las políticas y demás lineamientos puede generar sanciones.”</p>
    </div>
</script>

<script src="{{ asset('cajas/build/Login.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('cajas/css/login.css') }}" />
@endpush

@section('content')
    <div class="container login-subsido">
        <div class='row center-xs'>
            <div class='col-md-7 box-login'>
                <div class="logo mb-4 mt-4">
                    <img src="{{ asset('img/Mercurio/logo-min.png') }}" class="img-logo" style="width:90pt" />
                </div>
                <div class='login-item'>
                    <div class='col-xs-12'>
                        <h3>COMFACA EN LÍNEA <br /><small>(Administrativo)</small></h3>
                        <h4>POLÍTICA TRATAMIENTO DE DATOS:</h4>
                        <p class='text-justify'>
                            COMFACA identificado con Nit 891.190.047-2 es responsable del tratamiento de datos personales de su población afiliada incluyendo trabajadores, beneficiarios y empleadores, y en tal virtud informamos que al ingresar al sistema SISUWEB, usted como funcionario debe velar por la seguridad y confidencialidad de los datos, recuerde que la información debe ser protegida de acuerdo con nuestras políticas de protección de datos personales conforme a la Ley 1581 del 2012 y el decreto 1074 del 2015. <br />Todos los reportes generados con su usuario quedaran registrados bajo el mismo y usted asume la responsabilidad de acuerdo con lo declarado anteriormente.<br />
                            Para más información de la política puede vistar nuestro sitio web <br /> <a class='link-text' href='https://comfaca.dataprotected.co' target="_blank">comfaca.dataprotected.co</a></p>
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
                        <form id="form_autenticar" action="{{ route('cajas.autenticar') }}" method="POST">
                            @csrf
                            <div style="display: none;">
                                <input type="text" name="politica" id="politica" value="N"/>
                            </div>
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
                                    <button type='button' class='btn btn-md btn-primary my-4 btn-submit btn-block' id='bt_autenticar'>Autenticar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4">
        <div class="container">
            <div class="row align-items-center justify-content-xl-between">
                <div class="col-xl-12">
                    <div class="copyright text-center text-white">
                        &copy; 2022 <a href="http://comfaca.com/master" class="font-weight-bold ml-1 text-white" target="_blank">COMFACA.COM</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
@endsection
