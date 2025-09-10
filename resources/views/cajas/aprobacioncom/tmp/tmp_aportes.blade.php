<div class='pb-3'>
    <% if(_.size(aportes) == 0){ %>
    <table class='table table-bordered table-hover' id='table_aportes'>
        <tbody>
            <tr>
                <td>Ningún registro de pago de aportes disponible...</td>
            </tr>
        </tbody>
    </table>
    <% }else{
        _ai=1
        %>
    <table class='table table-sm align-items-center table-flush' id='table_aportes' width='100%'>
        <thead>
            <tr>
                <td>Periodo aportes</td>
                <td>Fecha recibo</td>
                <td>Fecha sistema</td>
                <td>Cedula trabajador</td>
                <td>Sucursal</td>
                <td>Nit</td>
                <td>Número</td>
                <td>Valor aportes</td>
                <td>Valor nomina</td>
            </tr>
        </thead>
        <tbody>
            <% _.each(aportes, function(row, ai){ %>
            <tr>
                <td><%=row.perapo%></td>
                <td><%=row.fecrec%></td>
                <td><%=row.fecsis%></td>
                <td><%=row.cedtra%></td>
                <td><%=row.codsuc%></td>
                <td><%=row.cedtra%></td>
                <td><%=row.numero%></td>
                <td><%=row.valapo%></td>
                <td><%=row.valnom%></td>
            </tr>
            <% _ai++ })} %>
        </tbody>
    </table>
    <br />
</div>