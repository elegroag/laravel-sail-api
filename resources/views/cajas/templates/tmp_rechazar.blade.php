<h4>Rechazar</h4>
<p>Esta opcion es para rechazar a la empresa e informarle la causal del rechazo</p>
<br />
<div class="col-md-12">
    <div class='form-group pb-2'>
        {{ Tag::select("codest", $mercurio11, "using: codest,detalle", "use_dummy: true", "dummyValue: ", "class: form-control") }}
    </div>
    <div class='form-group mt-2 pb-2'>
        <textarea class='form-control' id='nota_rechazar' rows='4'></textarea>
    </div>
    <div class="box form-group pt-3">
        <button data-cid='<%=id%>' type='button' class='btn btn-md btn-danger' style='width:250px' id='rechazar_solicitud'>Rechazar</button>
    </div>
</div>