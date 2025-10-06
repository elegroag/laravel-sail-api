@extends('layouts.cajas')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
@endpush

@section('content')
<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

@php echo \App\Services\Tag::ModalGeneric(
    'Configuración básica',
    view("cajas/mercurio01/tmp/form")->render(),
    'data-toggle="guardar"',
    'btCaptureModal',
    'capture-modal'
) @endphp

<script src="{{ asset('cajas/build/Basicas.js') }}"></script>

@endsection
