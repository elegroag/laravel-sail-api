<div class="col-md-6 col-auto">
    <h4 class="text-white d-inline-block mb-0"><%=titulo%></h4>
    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
            <li class="breadcrumb-item"><span class='text-white'><i class="fas fa-home"></i></span></li>
            <li class="breadcrumb-item"><span class='text-white'>&nbsp;|&nbsp;<%=breadcrumb_menu%> </span></li>
            <li class="breadcrumb-item"><span class='text-white'><%=(estado_detalle)? ' | '+ estado_detalle : '' %> </span></li>
        </ol>
    </nav>
</div>

<div class="col-md-6 col-auto text-right">
    <div class="dropdown">
        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-filter text-primary"></i> Filtrar Solicitudes
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <button type='button' data-toggle='linkFilter' data-valor='' class="dropdown-item"><i class="fas fa-angle-right text-primary"></i> Mostrar Todas</button>
            <button type='button' data-toggle='linkFilter' data-valor='T' class="dropdown-item <%=(estado=='T')?'disabled':'' %>"><i class="fas fa-angle-right text-primary"></i> Temporales</button>
            <button type='button' data-toggle='linkFilter' data-valor='P' class="dropdown-item <%=(estado=='P')?'disabled':'' %>"><i class=" fas fa-angle-right text-primary"></i> Pendiente</button>
            <button type='button' data-toggle='linkFilter' data-valor='D' class="dropdown-item <%=(estado=='D')?'disabled':'' %>"><i class=" fas fa-angle-right text-primary"></i> Devueltas</button>
            <button type='button' data-toggle='linkFilter' data-valor='R' class="dropdown-item <%=(estado=='R')?'disabled':'' %>"><i class=" fas fa-angle-right text-primary"></i> Rechazadas</button>
            <button type='button' data-toggle='linkFilter' data-valor='A' class="dropdown-item <%=(estado=='A')?'disabled':'' %>"><i class=" fas fa-angle-right text-primary"></i> Aprobadas</button>
        </div>
    </div>
    <% if (url_masivo) { %>
    <button type="button" data-href='<%=url_nueva%>' data-toggle='masivo' class='btn btn-sm btn-white border-0 mb-sm-3 mb-md-0'>
        <i class='fas fa-upload text-white'></i> Masivo</button>&nbsp;
    <% } %>

    <button type="button" data-href='<%=url_nueva%>' data-toggle='create' class='btn btn-sm btn-white border-0 mb-sm-3 mb-md-0 <%=create%>'>
        <i class='fas fa-plus text-primary'></i>&nbsp;Nueva
    </button>
</div>