@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
@csrf

<link rel="stylesheet" href="{{ asset('mercurio/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">

<script type="text/template" id='tmp_recovery'>
    {{ App\Services\View::renderView("auth/tmp/tmp_recovery") }}
</script>

<script type='text/template' id='tmp_login'>
    {{ App\Services\View::renderView("auth/tmp/tmp_login") }}
</script>

<script type="text/template" id='tmp_register'>
    {{ App\Services\View::renderView("auth/tmp/tmp_register") }}
</script>

<script type="text/template" id='tmp_verification'>
    {{ App\Services\View::renderView("auth/tmp/tmp_verification") }}
</script>

<script type="text/template" id='tmp_layout'>
    {{ App\Services\View::renderView("auth/tmp/tmp_layout") }}
</script>

<script type="text/template" id='tmp_email_change'>
    {{ App\Services\View::renderView("auth/tmp/tmp_email_change") }}
</script>

<script type="text/template" id='tmp_info'>
    {{ App\Services\View::renderView("auth/tmp/tmp_info") }}
</script>

{{ App\Services\View::renderView("auth/tmp/tmp_navbar_login") }}

<div id='boneLayout'></div>

<script src="{{ asset('mercurio/build/Login.js') }}"></script>

@endsection
