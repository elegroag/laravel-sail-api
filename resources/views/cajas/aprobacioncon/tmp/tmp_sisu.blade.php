<script id='tmp_conyuge' type='text/template'>
    <h4>Conyuge</h4>    
    <div class='row pl-lg-4 pb-3'>
        <div class='col-md-4 border-bottom border-top border-left'>
            <label class='form-control-label'>Cedcon:</label>
            <p class='descripcion'><%=cedcon%></p>
        </div>
        <div class='col-md-4 border-top border-bottom border-right border-left'>
            <label class='form-control-label'>Nombre:</label>
            <p class='descripcion'><%=prinom%> <%=segnom%> <%=priape%> <%=segape%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Estado: </label>
            <p class='descripcion'><%=(estado == 'A' || estado == 'D')? 'Activo': 'Inactivo'%></p>
        </div>
        <div class='col-md-4 border-bottom border-right'>
            <label class='form-control-label'>Fecha estado:</label>
            <p class='descripcion'><%=fecest%></p>
        </div>
        <div class='col-md-4 border-bottom border-left'>
            <label class='form-control-label'>Teléfono: </label>
            <p class='descripcion'><%=telefono%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Email: </label>
            <p class='descripcion'><%=email%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Tipo pago: </label>
            <p class='descripcion'><%=tippag%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Salario: </label>
            <p class='descripcion'><%=salario%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Codzon: </label>
            <p class='descripcion'><%=codzon%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Codigo cuenta: </label>
            <p class='descripcion'><%=codcue%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Numero cuenta: </label>
            <p class='descripcion'><%=numcue%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Dirección: </label>
            <p class='descripcion'><%=direccion%></p>
        </div>
        <div class='col-md-4 border-bottom border-right border-left'>
            <label class='form-control-label'>Estado civil: </label>
            <p class='descripcion'><%=estciv%></p>
        </div>
    </div>
</script>

<script id='tmp_relaciones' type='text/template'>
    <h4>Relaciones</h4>        
    <div class='row pl-lg-4 pb-3'>        
        <% if(_.size(relaciones) == 0){ %>
        <table class='table table-bordered table-hover'>
            <tbody>
                <tr>
                    <td>Ninguna dato de trayectoria disponible...</td>
                </tr>
            </tbody>
        </table>
        <% } else { %>
            <table class='table table-bordered table-hover'>
                <thead>
                    <tr>
                        <th>Trabajador</th>
                        <th>Compañera permanente</th>
                        <th>Fecha afiliación</th>
                        <th>Fecha sistema</th>
                        <th>Recibe Subsidio Otra Caja</th>
                        <th>Ruaf</th>
                    </tr>
                </thead>    
                <tbody>
                    <% _.each(relaciones, function(row){ %>
                        <tr>
                            <td><%=row.cedtra%></td>
                            <td><%=row.comper%></td>
                            <td><%=row.fecafi%></td>
                            <td><%=row.fecsis%></td>
                            <td><%=row.recsub%></td>
                            <td><%=row.ruaf%></td>
                        </tr>
                    <% }) %>
                </tbody>
            </table>
        <% } %>
    </div>    
</script>

<script id='tmp_card_header' type="text/template">
    <div class='row'>
        <div class='col-md-4'>
            <div class='row justify-content-first' style='padding-top:10px'>    
                <h3>&nbsp;Opción Informativa</h3>
            </div>
        </div>
        <div class='col-md-8'>
            <div id="botones" class='row justify-content-end'>
                <a href="#" data-href="info_conyuge/<?=$cedtra?>/<?=$cedcon?>/<?=$id?>" class='btn btn-sm btn-primary' id='cancelar_volver'><i class='fas fa-hand-point-up text-white'></i> Salir</a>&nbsp;   
            </div>
        </div>
    </div>
</script>

<div class="card-body">
    <div class="row">
        <div class="col-md-12" id='show_conyuge'></div>
        <div class="col-md-12" id='show_relaciones'></div>
    </div>    
</div>

<script type="text/javascript">
    const init_header = function(){

        let _template = _.template($("#tmp_card_header").html());
        $(".card-header").html(_template());

        $('#cancelar_volver').click(function(event){
            event.preventDefault();
            let _target = $(event.currentTarget);
            window.location.href = Utils.getKumbiaURL($Kumbia.controller + '/' +_target.attr('data-href'));
        });
    };

    const load_conyuge = function(){
        let _conyuge = <?=json_encode($conyuge)?>;
        console.log(_conyuge);
        let _template = _.template($("#tmp_conyuge").html());
        $("#show_conyuge").html(_template(_conyuge));
        
    };

    const load_relaciones = function(){
        let _relaciones = <?=json_encode($relaciones)?>;
        console.log(_relaciones);
        let _template = _.template($("#tmp_relaciones").html());
        $("#show_relaciones").html(_template({"relaciones": _relaciones}));
    };

    $(document).ready(function(){
        init_header();
        load_conyuge();
        load_relaciones();
    });
</script>

<style>
    .note-editable {
        background-color: #FFFFFF;
    }
    .note-editable p,
    .note-editable h3,
    .note-editable label,
    .note-editable span {
        line-height: 1.8pt;
    }
    .descripcion {
        font-size: .81rem;
        line-height: 1.05rem;
    }
    .form-control-label {
        font-size: .81rem;
        margin-bottom: 3px;
    }
    input.form-control,
    select.form-control {
        margin: 3px 0px 0px 3px;
        padding: 4px 6px;
        border-radius: 0px;
        height: initial;
        min-height: 20px;
        background-color: #fffeee;
    } 
    .card-header{
        padding-top:5px;
        padding-bottom: 5px;
    }
</style>