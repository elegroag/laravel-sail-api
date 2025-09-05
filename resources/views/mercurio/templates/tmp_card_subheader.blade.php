<script id='tmp_card_header' type="text/template">
    <div class="row">
        <div class="col-md-8">
            <ul class="nav nav-pills">
                 <% if(model !== false) { 
                    if(model.estado !== 'T'){ %>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" id="seguimiento-tab" href="#seguimiento" aria-controls="seguimiento" aria-selected="true">Seguimiento</a>
                    </li>
                <% } } %>
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" id="datos_solicitud-tab" href="#datos_solicitud" aria-controls="datos_solicitud" aria-selected="true">1 Datos empresa</a>
                </li>
                <% if(model !== false) { %>
                    <li class="nav-item" id='show_documentos'>
                        <a class="nav-link" data-bs-toggle="tab" id="documentos_adjuntos-tab" href="#documentos_adjuntos" aria-controls="documentos_adjuntos" aria-selected="true">2 Documentos Adjuntos</a>
                    </li>
                    <% if(model.estado === 'D' || model.estado === 'T') { %>
                    <li class="nav-item" id='show_enviarCaja'>
                        <a class="nav-link" data-bs-toggle="tab" id='enviarCaja' href="#enviar_radicado" aria-controls="enviar_radicado" aria-selected="true">3 Enviar Radicado <i class='fa fas fa-upload'></i></a>
                    </li>
                    <% } %>
                <% }%>
            </ul>        
        </div>
        <div class="col-md-4">
            <div id="botones" class='row justify-content-end'>
                <button type='button' id='btn_salir' data-href="<%=url_salir%>" class='btn btn-md btn-primary'>Salir</button>&nbsp;
            </div>
        </div>
    </div>
</script>

<div class='card-header' id='afiliacion_header' style='background-color:#dfdfdf'></div>