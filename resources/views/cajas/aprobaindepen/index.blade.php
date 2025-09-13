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

<script type="text/template" id='tmp_info'>
    @include('cajas/templates/tmp_information')
</script>

<script id='tmp_aportes' type='text/template'>
    @include('cajas/templates/tmp_aportes')
</script>

<script type="text/template" id='tmp_aprobar'>
    @include('cajas/aprobaindepen/tmp/tmp_aprobar')
</script>

<script id='tmp_info_header' type="text/template">
    @include('cajas/templates/tmp_info_header')
</script>

<script type="text/template" id='tmp_deshacer'>
    @include('cajas/templates/tmp_deshacer')
</script>

<script id='tmp_sisu' type='text/template'>
    @include('cajas/aprobaindepen/tmp/tmp_sisu')
</script>

<script id='tmp_notificar' type='text/template'>
    @include('cajas/templates/tmp_notificar')
</script>

<script type="text/template" id="tmp_reaprobar">
    @include('cajas/templates/tmp_reaprobar')
</script>

<script id='tmp_trayectoria' type='text/template'>
    <h4>Trayectoria</h4>
    <div class='row pl-lg-4 pb-3'>
        <% if(_.size(trayectoria) == 0){ %>
            <table class='table table-bordered table-hover'>
                <tbody>
                    <tr>
                        <td>Ninguna dato de trayectoria disponible...</td>
                    </tr>
                </tbody>
            </table>
        <% }else{
        _ai=1
        _.each(trayectoria, function(row, ai){ %>
            <table class='table table-bordered table-hover'>
            <tbody>
            <tr>
                <td rowspan='2' width='10pt' style="padding:2px"><%=_ai%></td>
                <td>Fecha inicia: <%=row.fecafi%></td>
            </tr>
            <tr>
                <td>Fecha retiro: <%=row.fecret%></td>
            </tr>
            </tbody>
        </table>
        <br/>
        <% _ai++ })} %>
    </div>
</script>

<script id='tmp_sucursales' type='text/template'>
    <h4>Sucursales</h4>
    <div class='row pl-lg-4 pb-3'>
        <%  if(_.size(sucursales) == 0){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td>Ninguna dato de sucursal disponible...</td>
                </tr>
            </tbody>
        </table>
        <% }else{
        _ai=1
        _.each(sucursales, function(row, ai){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td rowspan='2' width='10pt' style="padding:2px"><%=_ai%></td>
                    <td>Detalle: <%=row.detalle%></td>
                    <td>Código: <%=row.codsuc%></td>
                    <td>Codciu: <%=row.codciu%></td>
                </tr>
                <tr>
                    <td>Codzon: <%=row.codzon%></td>
                    <td>Ofiafi: <%=row.ofiafi%></td>
                    <td>Estado: <%=(row.estado == 'A')? 'Activo': 'Inactivo'%></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <% _ai++ })} %>
    </div>
</script>

<script id='tmp_listas' type='text/template'>
    <h4>Listas</h4>
    <div class='row pl-lg-4 pb-3'>
    <%  if(_.size(listas) == 0){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td>Ninguna dato de lista disponible...</td>
                </tr>
            </tbody>
        </table>
        <% }else{
        _ai=1
        _.each(listas, function(row, ai){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                <td rowspan='3' width='10pt' style="padding:2px"><%=_ai%></td>
                    <td>Detalle: <%=row.detalle %></td>
                </tr>
                <tr>
                    <td>Codlis: <%=row.codlis %></td>
                </tr>
                <tr>
                    <td>Ofiafi: <%=row.ofiafi %></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <% _ai++ })} %>
    </div>
</script>

<script id='tmp_empresa' type='text/template'>
    <div class="col-md-8" id='show_empresa'></div>
	<div class="col-md-4" id='show_trayectoria'></div>
	<hr />
	<div class="col-md-7" id='show_sucursales'></div>
	<div class="col-md-5" id='show_listas'></div>
</script>

<script id='tmp_table' type="text/template">
    <div id='consulta' class='table-responsive'></div>
	<div id='paginate' class='card-footer py-4'></div>
	<div id='filtro'></div>
</script>

<script src="{{ asset('Cajas/build/Independientes.js') }}"></script>
@endpush

@section('content')
<div id='boneLayout'></div>
@endsection
