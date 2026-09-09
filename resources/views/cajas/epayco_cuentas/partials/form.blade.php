<form id="form" class="validation_form" autocomplete="off" novalidate>
    <input type="hidden" id="id" name="id" value="">

    <div class="form-group">
        <label for="account" class="form-control-label">Nombre de cuenta (account) *</label>
        <input type="text" class="form-control" id="account" name="account" maxlength="120" required>
        <small class="form-text text-muted">Identificador único para distinguir la cuenta.</small>
    </div>

    <div class="form-group">
        <label for="env_mode" class="form-control-label">Ambiente (env_mode) *</label>
        <select id="env_mode" name="env_mode" class="form-control" required>
            <option value="development">development</option>
            <option value="production">production</option>
        </select>
    </div>

    <div class="form-group">
        <label for="p_id_customer" class="form-control-label">P_CUST_ID_CLIENTE (p_id_customer) *</label>
        <input type="text" class="form-control" id="p_id_customer" name="p_id_customer" maxlength="80" required>
    </div>

    <div class="form-group">
        <label for="public_key" class="form-control-label">Public key *</label>
        <input type="text" class="form-control" id="public_key" name="public_key" maxlength="255" required>
    </div>

    <div class="form-group">
        <label for="private_key" class="form-control-label">Private key <span id="private_key_req">*</span></label>
        <div class="input-group">
            <input type="password" class="form-control" id="private_key" name="private_key" maxlength="255" autocomplete="new-password">
            <button type="button" class="input-group-text btn btn-outline-secondary" data-toggle-secret="private_key" title="Mostrar / ocultar" aria-label="Mostrar u ocultar private key">
                <i class="fa fa-eye" data-eye-icon></i>
            </button>
        </div>
        <small class="form-text text-muted" id="private_key_help">Requerido al crear. En edición, deje vacío para no cambiar.</small>
    </div>

    <div class="form-group mb-0">
        <label for="p_key" class="form-control-label">P_KEY (firma webhook) <span id="p_key_req">*</span></label>
        <div class="input-group">
            <input type="password" class="form-control" id="p_key" name="p_key" maxlength="255" autocomplete="new-password">
            <button type="button" class="input-group-text btn btn-outline-secondary" data-toggle-secret="p_key" title="Mostrar / ocultar" aria-label="Mostrar u ocultar P_KEY">
                <i class="fa fa-eye" data-eye-icon></i>
            </button>
        </div>
        <small class="form-text text-muted" id="p_key_help">Requerido al crear. En edición, deje vacío para no cambiar.</small>
    </div>
</form>
