@extends('layouts.auth')

@section('title', 'Iniciar Sesión')
@section('application', 'mercurio')

@section('content')
<link rel="stylesheet" href="{{ asset('mercurio/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">

<script type="text/template" id='tmp_recovery'>
    @include('mercurio/auth/tmp/tmp_recovery')
</script>

<script type='text/template' id='tmp_login'>
    @include('mercurio/auth/tmp/tmp_login')
</script>

<script type="text/template" id='tmp_register'>
    @include('mercurio/auth/tmp/tmp_register')
</script>

<script type="text/template" id='tmp_verification'>
    @include('mercurio/auth/tmp/tmp_verification')
</script>

<script type="text/template" id='tmp_layout'>
    @include('mercurio/auth/tmp/tmp_layout')
</script>

<script type="text/template" id='tmp_email_change'>
    @include('mercurio/auth/tmp/tmp_email_change')
</script>

<script type="text/template" id='tmp_info'>
    @include('mercurio/auth/tmp/tmp_info')
</script>

@include('mercurio/auth/tmp/tmp_navbar_login')

<div id='boneLayout'></div>

<script src="{{ asset('mercurio/build/Login.js') }}"></script>

@endsection
