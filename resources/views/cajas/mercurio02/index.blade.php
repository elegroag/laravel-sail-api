@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@section('content')
<div id='consulta' class='table-responsive'></div>
<div id='paginate' class='card-footer py-4'></div>

<!-- Modal Captura -->
@php echo Tag::ModalGeneric(
    $title,
    View::render("mercurio02/tmp/form", array('ciudades' => $ciudades))
)
@endphp
@endsection

@section('scripts')
<script src="{{ asset('Cajas/build/Mercuro02.js') }}"></script>
@endpush
