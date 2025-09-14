@extends('layouts.cajas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/choices/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables.net.bs5/css/dataTables.bootstrap5.css') }}" />
@endpush

@push('scripts')
    <script id='tmp_filtro' type="text/template">
        @php echo Tag::filtro($campo_filtro, 'aplicar_filtro'); @endphp
    </script>

    <script type="text/template" id='tmp_layout'>
        @php echo View::renderView("usuario/tmp/tmp_layout"); @endphp
    </script>

    <script type="text/template" id='tmp_header'>
        @php echo View::renderView("templates/tmp_header"); @endphp
    </script>

    <script type="text/template" id='tmp_tabla_usuarios'>
        <div  class='table-excel-wrapper' id='consulta'></div>
        <div class='mt-3' id='paginate'></div>
        <div><p>Total de registros: <span id='total_registros'></span> consultados.</p></div>
        <div id='filtro'></div>
    </script>

    <script type="text/template" id='tmp_detalle'>
        @php echo View::renderView("usuario/tmp/tmp_detalle"); @endphp
    </script>

    <script id='tmp_list_header' type="text/template">
        <div class="col">
            <div id="botones" class='d-flex justify-content-end'>
            <button type='button' data-tipo="" data-toggle='link' class='btn btn-sm btn-default'>Todos</button>&nbsp;
            <button type='button' data-tipo="P" data-toggle='link' class='btn btn-sm btn-default'>Particulares</button>&nbsp;
                <button type='button' data-tipo="E" data-toggle='link' class='btn btn-sm btn-default'>Empresas</button>&nbsp;
                <button type='button' data-tipo="T" data-toggle='link' class='btn btn-sm btn-default'>Trabajadores</button>&nbsp;
                <button type='button' data-tipo="I" data-toggle='link' class='btn btn-sm btn-default'>Independientes</button>&nbsp;
                <button type='button' data-tipo="O" data-toggle='link' class='btn btn-sm btn-default'>Pensionados</button>&nbsp;
            </div>
        </div>
    </script>

    <script src="{{ asset('Cajas/build/Usuario.js') }}"></script>
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection
