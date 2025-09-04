@extends('layouts.bone')

@section('content')

<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">

<script type="text/template" id='tmp_layout'>
    @include('principal.tmp.tmp_layout')
</script>

<script type="text/template" id='tmp_card'>
    @include('principal.tmp.tmp_card')
</script>

<div id='boneLayout'></div>

<script src="{{ asset('mercurio/build/Principal.js') }}"></script>

@endsection
