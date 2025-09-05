<div class="col-6">
    <fieldset>
        <legend>Firma Requerida</legend>
        <ul class="list-group">

            <li class="list-group-item mb-2">
                <p>
                    <button type="button" data-bs-toggle='new-firma' class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i>
                    </button> Crear la firma la cúal es requerida
                </p>
            </li>
        </ul>
    </fieldset>
    <br />
    <fieldset>
        <legend>Firmas Creada</legend>
        <ul class="list-group">
            <% if(firma === false){ %>
            <li class="list-group-item mb-2">
                <p>No dispone de una firma creada</p>
            </li>
            <% }else{ %>

            <li class="list-group-item border-top-0 border-left-0 border-right-0 pb-0 mb-2">
                <div class="row">
                    <div class="col-5">Firma del <b><%=(firma.representa == 'E')? 'EMPLEADOR' : 'TRABAJDOR' %></b> <br />
                        Creada el día <%=firma.fecha%>
                    </div>
                    <div class="col-auto">
                        <img src=' <?= public_url() ?><%=firma.firma%>' />
                    </div>
                </div>
            </li>
            <% } %>
        </ul>
    </fieldset>
</div>

<div class="col-6">
    <fieldset class="p-4">
        <legend>¿Por qué usar una firma Digital?</legend>
        <p style="font-size: 1rem;">
            El objetivo principal de tener un documento con firma digital es garantizar la integridad,
            autenticidad y no repudio del contenido del documento, así como la identidad del firmante.
            Al utilizar una firma digital.</p>
        <ul>
            <li>
                <b>Integridad del documento:</b><br />La firma digital protege contra cualquier modificación no autorizada del contenido del documento. Si el contenido del documento se altera después de que se haya firmado digitalmente, la firma digital se volverá inválida, lo que indica que el documento ha sido comprometido.
            </li>
            <li>
                <b>Autenticidad del documento:</b><br /> La firma digital proporciona una garantía de que el documento proviene del firmante declarado y no ha sido modificado desde que fue firmado. Esto ayuda a verificar la autenticidad del origen del documento y asegura que no haya sido falsificado o alterado por terceros.
            </li>
            <li>
                <b>Seguridad y confianza:</b><br /> La firma digital proporciona un mecanismo seguro y confiable para validar la autenticidad e integridad de los documentos electrónicos. Esto ayuda a crear un entorno de confianza en el intercambio de información y transacciones en línea.
            </li>
        </ul>
    </fieldset>
</div>