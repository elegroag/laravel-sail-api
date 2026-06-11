<div id="breadcrumb-bar"></div>

<div class="header bg-gradient-primary pb-9">
    <div class="container-fluid">
        <div class="header-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <nav aria-label="breadcrumb" class="mb-0">
                    <ol class="breadcrumb mb-0 bg-transparent text-white">
                        <li class="breadcrumb-item"><a href="#" class="text-white text-decoration-none opacity-75">Inicio</a></li>
                        <li class="breadcrumb-item active text-white fw-normal" aria-current="page" id="breadcrumb-current"><%= window.BREADCRUMB_TITLE || 'Listar solicitudes' %></li>
                    </ol>
                </nav>
                <div id='header_group_button'></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--9 pb-4">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class='card-header bg-green-blue p-1' id='render_subeader'></div>
                <div class="card-body m--3">
                    <div id="app"></div>
                </div>
            </div>
        </div>
    </div>
</div>