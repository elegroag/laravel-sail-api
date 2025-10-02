@extends('layouts.bone')

@section('content')
<div id='boneLayout'>
    <div class="card-body m-3">
        <div id="app" class="row">
            <div class="col-md-6">
                <h5 class="text-primary">Clave publica Firma Digital</h5>
                <p>Para comprobar la autenticidad de un documento digital, puede hacer uso del siguiente certificado publico de firma digital.</p>
                <code>
                    {{$publicKey}}
                </code>
            </div>
            <div class="col-md-6">
                <div id="fileUpload" class="file-container">
                    <label for="fileUpload-1" class="file-upload">
                        <div>
                            <b class="material-icons-outlined">Validar Documento</b>
                            <p>Arrastra y suelta archivo aquí</p>
                            <span>O</span>
                            <div>Click buscar archivos</div>
                        </div>
                        <input type="file" id="fileUpload-1" name="[]" multiple="" hidden="">
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('mercurio/build/Firmas.js') }}"></script>
@endpush

@push('styles')
<style>
    :root {
        --file-container-bg: #eee;
        --file-bg: #f8f8f8;
        --file-border-color: #606060;
        --file-rounded: 15px;
        --file-color: #2b2b2b;
        --table-border-color: #efefef;
        --delete-button-bg: #f53636;
        --delete-button-color: #fff;
        --font-size: 0.875em;
        --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        --shadow: 0px 8px 15px -8px rgba(0, 0, 0, 0.5);
    }

    .file-container {
        width: 100%;
        font-family: var(--font-family);
    }

    .file-container .file-upload {
        width: 100%;
        display: flex;
        background-color: var(--file-container-bg);
        border-radius: var(--file-rounded);
        transition: all 0.3s;
    }

    .file-container .file-upload:hover {
        box-shadow: var(--shadow);
    }

    .file-container .file-upload>div {
        width: 100%;
        background-color: var(--file-bg);
        padding: 25px;
        margin: 25px;
        border-radius: 10px;
        border: 1px dashed var(--file-border-color);
        text-align: center;
        cursor: pointer;
    }

    .file-container .file-upload>div>b {
        font-size: 2.125em;
        color: var(--file-color);
    }

    .file-container .file-upload>div>p,
    .file-container .file-upload>div span,
    .file-container .file-upload>div div {
        font-size: var(--font-size);
        line-height: 30px;
        color: var(--file-color);
    }

    .file-container .file-upload>div>div {
        width: max-content;
        padding: 0 10px;
        margin: 0 auto;
        border: 1px solid var(--file-border-color);
        border-radius: 8px;
    }

    .file-container>table {
        width: 100%;
        border-collapse: collapse;
        font-size: var(--font-size);
        margin-top: 20px;
    }

    .file-container>table th,
    .file-container>table td {
        border-bottom: 1px solid var(--table-border-color);
        padding: 8px;
        text-align: left;
    }

    .file-container>table>tbody>tr>td:nth-child(1) {
        font-weight: bold;
    }

    .file-container>table>tbody>tr>td:nth-child(2) {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 1px;
    }

    .file-container>table>tbody>tr>td:nth-child(3) {
        text-align: center;
    }

    .file-container>table>tbody>tr>td>img {
        border-radius: 5px;
        box-shadow: var(--shadow);
    }

    .file-container>table>tbody>tr>td.no-file {
        text-align: center;
        font-weight: normal;
    }

    .file-container>table>tbody>tr>td>i {
        font-size: 1.125em;
    }

    .file-container>table button {
        display: inline-block;
    }

    .file-container>table button:hover {
        box-shadow: var(--shadow);
    }

    .file-container>table button>i {
        color: var(--delete-button-color);
        font-size: 1.125em;
    }
</style>
@endpush
