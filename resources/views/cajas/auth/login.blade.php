@extends('layouts.auth')

@section('application', 'cajas')

@push('scripts')
<script src="{{ versioned_asset('cajas/build/Login.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('cajas/css/login.css') }}" />
@endpush

@section('content')
    <div class="login-wrapper">
        <div class="login-container">
            <section class="login-panel login-panel--info">
                <div class="login-panel__logo">
                    <img src="{{ asset('img/Mercurio/logo-min.png') }}" alt="Comfaca" />
                </div>
                <div class="login-panel__body">
                    <h3>COMFACA EN LÍNEA<br /><small>(Administrativo)</small></h3>
                    <h4>POLÍTICA TRATAMIENTO DE DATOS:</h4>
                    <p class='text-justify'>
                        COMFACA identificado con Nit 891.190.047-2 es responsable del tratamiento de datos personales de su población afiliada incluyendo trabajadores, beneficiarios y empleadores, y en tal virtud informamos que al ingresar al sistema SISUWEB, usted como funcionario debe velar por la seguridad y confidencialidad de los datos, recuerde que la información debe ser protegida de acuerdo con nuestras políticas de protección de datos personales conforme a la Ley 1581 del 2012 y el decreto 1074 del 2015.
                    </p>
                    <p class='text-justify'>
                        Todos los reportes generados con su usuario quedaran registrados bajo el mismo y usted asume la responsabilidad de acuerdo con lo declarado anteriormente.
                    </p>
                    <p class='text-justify'>
                        Para más información de la política puede vistar nuestro sitio web
                        <a class='link-text' href='https://comfaca.dataprotected.co' target="_blank">comfaca.dataprotected.co</a>
                    </p>
                </div>
            </section>

            <section class="login-panel login-panel--form">
                <div class="login-panel__inner">
                    <header class="login-panel__header">
                        <h3 id='titulo_autenticacion_opcion'>Iniciar Sesíon</h3>
                        <p class='decripcion'>Los siguientes campos son requeridos para el proceso de autenticación.</p>
                    </header>

                    <form id="form_autenticar" action="{{ route('cajas.autenticar') }}" method="POST" class="login-form">
                        @csrf
                        <input type="hidden" name="politica" id="politica" value="N" />

                        <p class='error_user error'></p>
                        <div class="form-group">
                            <div class="input-group input-group-merge input-group-alternative">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-circle-08"></i></span>
                                </div>
                                <input class="form-control pl-1" id="user" name="user" placeholder="Usuario" type="text">
                            </div>
                        </div>

                        <p class='error_clave error'></p>
                        <div class="form-group">
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

                        <p class='error_captcha error'></p>
                        <div class="form-group captcha-box">
                            <img src="{{ route('cajas.captcha.image') }}?v={{ time() }}" id="captcha_image" alt="Codigo de verificacion" aria-label="Codigo captcha">
                            <a href="#" id="reload_captcha" title="Generar nuevo codigo" aria-label="Recargar codigo captcha">
                                <i class="fa fa-refresh"></i>
                            </a>
                            <input class="form-control pl-1" id="captcha" name="captcha" placeholder="Codigo" type="text" autocomplete="off" required maxlength="8" aria-label="Ingresar codigo captcha">
                        </div>

                        <div class="form-group login-form__submit">
                            <button type='button' class='btn btn-md btn-primary btn-block btn-submit' id='bt_autenticar'>Autenticar</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <footer class="login-footer">
        <div class="copyright text-center text-white">
            &copy; 2022 <a href="http://comfaca.com/master" class="font-weight-bold ml-1 text-white" target="_blank">COMFACA.COM</a>
        </div>
    </footer>
@endsection
