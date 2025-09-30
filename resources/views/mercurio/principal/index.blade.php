@extends('layouts.bone')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">

<style>
    .total-affiliations-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 25px;
        max-width: 350px;
        margin: auto; /* Centra la tarjeta */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .total-affiliations-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .total-affiliations-title {
        font-size: .87rem;
        color: #8c5f5f; /* Color marrón rojizo */
        font-weight: 600;
    }

    .people-icon {
        width: 20px;
        height: 20px;
        /* SVG del icono de personas con el color verde correcto */
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="%238c8f5f" class="bi bi-people-fill" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>');
        background-size: contain;
        background-repeat: no-repeat;
    }

    .total-value {
        font-size: .87rem;
        color: #4e4e4e;
    }
</style>
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection

@push('scripts')
<script type="text/template" id='tmp_layout'>
    @include('mercurio/principal.tmp.tmp_layout')
</script>

<script type="text/template" id='tmp_card'>
    @include('mercurio/principal.tmp.tmp_card')
</script>

<script src="{{ asset('mercurio/build/Principal.js') }}"></script>
@endpush