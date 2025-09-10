<div class="col-lg-7 col-auto mr-auto">
    <h4 class="text-white d-inline-block mb-0"><%= titulo %></h4>
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
            <li class="breadcrumb-item">
                <a class="btn btn-sm" data-href="principal/index"><i class="fas fa-home"></i></a>
                <label class="text-white"><%= detalle %></label>
            </li>
        </ol>
    </nav>
</div>
<div class="col-lg-5 col-auto text-right" id='navbarTags'>
    <button type='button' class='btn btn-default text-black btn-icon-only rounded-circle' info='Filtro' id='btFiltrar' <%=(info)? '': 'disabled' %>>
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <button type='button' class='btn btn-default text-black btn-icon-only rounded-circle' id='btListar' <%=(info)? 'disabled': '' %>>
        <i class="fa fa-list-alt" aria-hidden="true"></i>
    </button>
    <button type='button' class='btn btn-default text-black btn-icon-only rounded-circle' id='btSalir'>
        <i class="fa fa-undo" aria-hidden="true"></i>
    </button>
</div>