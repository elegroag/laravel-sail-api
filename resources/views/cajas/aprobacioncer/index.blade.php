@php
    use App\Services\Tag;
@endphp

@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
<script id='tmp_filtro' type="text/template">
    @php echo Tag::filtro($campo_filtro, 'aplicar_filtro') @endphp
</script>

<script id='tmp_list_header' type="text/template">
    @include('cajas/templates/tmp_list_header')
</script>

<script type="text/template" id='tmp_layout'>
    @include('cajas/templates/tmp_layout')
</script>

<script type="text/template" id='tmp_header'>
    @include('cajas/templates/tmp_header')
</script>

<script type="text/template" id='tmp_rechazar'>
    @include('cajas/templates/tmp_rechazar')
</script>

<script type="text/template" id='tmp_devolver'>
    @include('cajas/templates/tmp_devolver')
</script>

<script type="text/template" id="tmp_reaprobar">
    @include('cajas/templates/tmp_reaprobar')
</script>

<script type="text/template" id='tmp_aprobar'>
    @include('cajas/aprobacioncer/tmp/tmp_aprobar')
</script>

<script type="text/template" id='tmp_info'>
    @include('cajas/templates/tmp_information')
</script>

<script type="text/template" id='tmp_info_header'>
    @include('cajas/templates/tmp_info_header')
</script>

<script id='tmp_info_header' type="text/template">
    <div class="ml-3">
		<div class='row justify-content-start'>
			<div id="botones" class='row justify-content-end'>
				<button type='button' class='btn btn-sm btn-info text-white' toggle-event="volver">
					Volver</button>&nbsp;
			</div>
		</div>
	</div>
</script>

<script type="text/template" id='tmp_table'>
	<div id='consulta' class='table-responsive'></div>
	<div id='paginate' class='card-footer py-4'></div>
	<div id='filtro'></div>
</script>

<script src="{{ asset('Cajas/build/Certificados.js') }}"></script>
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection
