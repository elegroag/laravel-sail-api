@extends('layouts.bone')

@section('title', 'Principal')

@section('content')
@csrf

<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">

<script type="text/template" id='tmp_layout'>
    {{ App\Services\View::renderView("principal/tmp/tmp_layout") }}
</script>

<script type="text/template" id='tmp_card'>
    {{ App\Services\View::renderView("principal/tmp/tmp_card") }}
</script>

<div id='boneLayout'></div>

<script src="{{ asset('mercurio/build/Principal.js') }}"></script>

@endsection
