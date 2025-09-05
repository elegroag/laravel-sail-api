<div class='col-12'>
    <p class="m-2">Seguimiento del proceso de afiliación</p>
    <div class="row pl-lg-4 pb-3">
        <% if (_.size(seguimientos) > 0) { %>
        <div class="col-12">
            <div class="timeline">
                <% _.each(seguimientos, (seguimiento, index) => { %>
                <% const isActive = index === 0; %>
                <div class="timeline-item <%= isActive ? 'active' : '' %>"
                    data-tipopc="<%= seguimiento.tipopc %>"
                    data-id="<%= seguimiento.numero %>">

                    <div class="timeline-badge"></div>

                    <div class="timeline-panel p-3 mb-4 shadow-sm">
                        <div class="timeline-heading">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="timeline-title mb-0 text-primary">
                                    <%= estados_detalles[seguimiento.estado] %>
                                </p>
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i><%= seguimiento.fecsis %>
                                    <% if(isActive) { %>
                                    <span class="badge bg-success ms-2">Último evento</span>
                                    <% } %>
                                </small>
                            </div>
                        </div>

                        <div class="timeline-body">
                            <p class="mb-2"><i class="fas fa-comment me-2"></i><%= seguimiento.nota %></p>

                            <% if (seguimiento.corregir && seguimiento.corregir[0] != '') { %>
                            <div class="alert alert-warning p-2 mt-2 mb-0">
                                <h6 class="alert-heading mb-2">Documentos requeridos:</h6>
                                <ul class="list-unstyled mb-0">
                                    <% _.each(seguimiento.corregir, (_val) => { %>
                                    <li class="mb-1">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        CORREGIR: <%= campos_disponibles[_val] %>
                                    </li>
                                    <% }) %>
                                </ul>
                            </div>
                            <% } %>
                        </div>
                    </div>
                </div>
                <% }) %>
            </div>
        </div>
        <% } else { %>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>No dispone de eventos de seguimiento para mostrar.
            </div>
        </div>
        <% } %>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 50px;
        margin: 20px 0;
    }

    .timeline:before {
        content: '';
        position: absolute;
        width: 2px;
        background-color: #e9ecef;
        top: 0;
        bottom: 0;
        left: 15px;
        margin-left: -1px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-badge {
        position: absolute;
        left: -50px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #c1cdd7;
        color: white;
        text-align: center;
        line-height: 30px;
        z-index: 1;
    }

    .timeline-item.active .timeline-badge {
        background-color: #84d84d;
        box-shadow: 0 0 0 3px rgba(13, 253, 201, 0.2);
    }

    .timeline-panel {
        position: relative;
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .timeline-item.active .timeline-panel {
        border-left: 3px solid #84d84d;
    }

    .timeline-title {
        color: #84d84d;
        font-weight: 400;
        font-size: 1.2rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-body {
        padding: 4px 10px
    }
</style>