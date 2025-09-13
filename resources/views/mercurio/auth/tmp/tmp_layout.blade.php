<div class="main-content mt-5">
    <div class='container pb-3'>
        <div class='row'>
            <div class="col-12" id='render_register'></div>
            <div class="col-12" id='render_verification'></div>
            <div class="row justify-content-between" id='render_sesion'>
                <div class="col-sm-12 col-md-5 pl-5 pr-3">
                    <span id='render_login'></span>
                    <span id='render_recovery'></span>
                </div>
                <% if (useInfo) { %>
                <div class='col-sm-12 col-md-7 pl-5 pr-3' id='render_info'></div>
                <% } %>
            </div>
        </div>
    </div>
</div>