<div class="header bg-gradient-primary pb-9">
    <div class="container-fluid">
        <div class="row header-body p-3">
            <div id="header_group_button">
                <div class="d-flex justify-content-center">
                    <div class="col-lg-7 col-auto mr-auto">
                        <h4 class="text-white d-inline-block mb-0">{{ $sub_title }}</h4>
                    </div>
                    <div class="col-lg-5 col-auto text-right" id="navbarTags">
                        @if ($filtrar)
                            <button 
                                type="button" 
                                class="btn btn-default text-black btn-icon-only rounded-circle" 
                                info="Filtro" 
                                id="btFiltrar"
                                data-toggle='header-filtrar'>
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if ($listar)
                            <button 
                                type="button" 
                                class="btn btn-default text-black btn-icon-only rounded-circle" 
                                data-toggle='header-listar'
                                id="btListar">
                                <i class="fa fa-list-alt" aria-hidden="true"></i>
                            </button>
                        @endif
                        @if ($add)
                            <button 
                                type="button" 
                                class="btn btn-default text-black btn-icon-only rounded-circle" 
                                id="btAdd"
                                data-toggle='header-nuevo'>
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                        @endif
                        @if ($salir)
                            <button 
                                type="button" 
                                class="btn btn-default text-black btn-icon-only rounded-circle" 
                                data-toggle='header-salir'
                                id="btSalir">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>