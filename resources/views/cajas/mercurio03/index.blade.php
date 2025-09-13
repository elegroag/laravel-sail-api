@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
@php echo Tag::filtro($campo_filtro); @endphp

@php echo Tag::ModalGeneric(
    $title,
    View::render("mercurio03/tmp/form")
) @endphp

<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>
@endsection

@section('scripts')
<script src="{{ asset('Cajas/build/Firmas.js') }}"></script>
@endpush
