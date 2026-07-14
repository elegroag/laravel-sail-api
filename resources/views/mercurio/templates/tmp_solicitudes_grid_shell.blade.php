<div class="solicitudes-toolbar">
    <div class="solicitudes-toolbar__panel">
        <div class="solicitudes-toolbar__search">
            <label class="solicitudes-toolbar__label" for="solicitudes-search">Buscar</label>
            <div class="input-group input-group-sm solicitudes-toolbar__search-group">
                <input
                    type="search"
                    id="solicitudes-search"
                    class="form-control solicitudes-search"
                    placeholder="Radicado, nombre o documento..."
                    autocomplete="off"
                />
                <button
                    type="button"
                    id="solicitudes-search-btn"
                    class="btn btn-primary solicitudes-toolbar__search-btn"
                    title="Buscar"
                    aria-label="Buscar solicitudes"
                >
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <span class="solicitudes-toolbar__btn-text">Buscar</span>
                </button>
            </div>
        </div>

        <div class="solicitudes-toolbar__actions">
            <div class="solicitudes-toolbar__group">
                <span class="solicitudes-toolbar__label solicitudes-toolbar__label--static">Acciones</span>
                <button
                    type="button"
                    id="solicitudes-reload"
                    class="btn btn-outline-secondary btn-sm solicitudes-toolbar__action-btn"
                    title="Recargar listado"
                    aria-label="Recargar listado"
                >
                    <i class="fas fa-sync-alt" aria-hidden="true"></i>
                    <span class="solicitudes-toolbar__btn-text">Recargar</span>
                </button>
            </div>

            <div class="solicitudes-toolbar__divider" aria-hidden="true"></div>

            <div class="solicitudes-toolbar__group solicitudes-toolbar__group--page-size">
                <label class="solicitudes-toolbar__label" for="solicitudes-page-size">Por página</label>
                <select
                    id="solicitudes-page-size"
                    class="form-select form-select-sm solicitudes-toolbar__page-size"
                    aria-label="Registros por página"
                >
                    <option value="10" selected>10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>

            <div class="solicitudes-toolbar__divider" aria-hidden="true"></div>

            <div class="solicitudes-toolbar__group solicitudes-toolbar__group--pagination">
                <span class="solicitudes-toolbar__label solicitudes-toolbar__label--static">Página</span>
                <div class="solicitudes-toolbar__pagination" role="navigation" aria-label="Paginación de solicitudes">
                    <button
                        type="button"
                        id="solicitudes-prev"
                        class="btn btn-outline-secondary btn-sm solicitudes-toolbar__page-btn"
                        title="Página anterior"
                        aria-label="Página anterior"
                        disabled
                    >
                        <i class="fas fa-chevron-left" aria-hidden="true"></i>
                    </button>
                    <span id="solicitudes-page-info" class="solicitudes-toolbar__page-info" aria-live="polite">0 / 0</span>
                    <button
                        type="button"
                        id="solicitudes-next"
                        class="btn btn-outline-secondary btn-sm solicitudes-toolbar__page-btn"
                        title="Página siguiente"
                        aria-label="Página siguiente"
                        disabled
                    >
                        <i class="fas fa-chevron-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="consulta" class="solicitudes-grid"></div>
<div id="solicitudes-empty" class="solicitudes-empty d-none" role="status">
    ¡No hay solicitudes disponibles para mostrar!
</div>
<div id="solicitudes-summary" class="solicitudes-summary" role="status" aria-live="polite">
    Mostrando 0 registros · Total existentes: 0
</div>
