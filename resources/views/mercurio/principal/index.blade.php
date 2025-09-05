@extends('layouts.bone')

@push('styles')
<link rel="stylesheet" href="{{ asset('mercurio/css/principal.css') }}">
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

