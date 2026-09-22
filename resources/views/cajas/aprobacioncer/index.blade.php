@extends('layouts.cajas-request')

@push('scripts')
{{-- Override del layout: en certificados no aplica "campos para corregir" --}}
<script type="text/template" id='tmp_devolver'>
    @include('cajas/aprobacioncer/tmp/tmp_devolver')
</script>

<script id='tmp_filtro' type="text/template">
    @include('cajas/templates/tmp_filtro', ['campo_filtro' => $campo_filtro])
</script>

<script type="text/template" id='tmp_aprobar'>
    @include('cajas/aprobacioncer/tmp/tmp_aprobar')
</script>

<script src="{{ versioned_asset('cajas/build/Certificados.js') }}"></script>
@endpush
