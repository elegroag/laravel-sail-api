@extends('layouts.bone')

@section('content')
<div class="card-body">

    <div class="nav-wrapper">
        <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-bs-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Empresas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-2-tab" data-bs-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Facultativo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-3-tab" data-bs-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Pensionado
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-4-tab" data-bs-toggle="tab" href="#tabs-icons-text-4" role="tab" aria-controls="tabs-icons-text-4" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Comunitaria
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-icons-text-5-tab" data-bs-toggle="tab" href="#tabs-icons-text-5" role="tab" aria-controls="tabs-icons-text-5" aria-selected="true">
                    <i class="fas fa-user-tie  mr-2"></i>Domestico
                </a>
            </li>
        </ul>
    </div>
    <div class="card shadow">
        <div class="card-body pt-0">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <hr />
                    <div class="row">
                        {{ $html_empresa }}
                    </div>
                </div>
                <div class="tab-pane fade show " id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                    <hr />
                    <div class="row">
                        {{ $html_facultativo }}
                    </div>
                </div>
                <div class="tab-pane fade show " id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                    <hr />
                    <div class="row">
                        {{ $html_pensionado }}
                    </div>
                </div>
                <div class="tab-pane fade show " id="tabs-icons-text-4" role="tabpanel" aria-labelledby="tabs-icons-text-4-tab">
                    <hr />
                    <div class="row">
                        {{ $html_comunitaria }}
                    </div>
                </div>
                <div class="tab-pane fade show " id="tabs-icons-text-5" role="tabpanel" aria-labelledby="tabs-icons-text-5-tab">
                    <hr />
                    <div class="row">
                        {{ $html_domestico }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
