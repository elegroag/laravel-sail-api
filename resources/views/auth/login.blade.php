<?php
use App\Services\View;
?>
@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
    @csrf
    <script type="text/template" id='tmp_recovery'>
        <?= View::renderView("auth/tmp/tmp_recovery"); ?>	
    </script>

    <script type='text/template' id='tmp_login'>
        <?= View::renderView("auth/tmp/tmp_login"); ?>	
    </script>

    <script type="text/template" id='tmp_register'>
        <?= View::renderView("auth/tmp/tmp_register"); ?>	
    </script>

    <script type="text/template" id='tmp_verification'>
        <?= View::renderView("auth/tmp/tmp_verification"); ?>	
    </script>

    <script type="text/template" id='tmp_layout'>
        <?= View::renderView("auth/tmp/tmp_layout"); ?>
    </script>

    <script type="text/template" id='tmp_email_change'>
        <?= View::renderView("auth/tmp/tmp_email_change"); ?>
    </script>

    <script type="text/template" id='tmp_info'>
        <?= View::renderView("auth/tmp/tmp_info"); ?>
    </script>

    <? View::renderView("login/tmp/tmp_navbar_login"); ?>

    <div id='boneLayout'></div>
    <script src="{{ asset('mercurio/build/login.js') }}"></script>
@endsection
