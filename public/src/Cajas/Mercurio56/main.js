$(document).ready(function() {
	validator = $("#form").validate({
		rules: {
			codinf: { required: true },
			email: { required: true },
			telefono: { required: true },
			nota: { required: true },
			estado: { required: true },
			archivo: { required: true }
		}
	});

	$("#codinf").blur(function() {
		validePk();
	});

	$("#capture-modal").on("hide.bs.modal", function(e) {
		validator.resetForm();
		$(".select2-selection").removeClass(validator.settings.errorClass).removeClass(validator.settings.validClass);
	});
	aplicarFiltro();
});

function guardar() {
	if (!$("#form").valid()) {
		return;
	}
	var archivo = $("#archivo").val();
	if (archivo == "") {
		Messages.display("Adjunte el Archivo", "error");
		return;
	}
	$("#form :input").each(function(elem) {
		$(this).attr("disabled", false);
	});
	$("#archivo").upload(
		Utils.getKumbiaURL($Kumbia.controller + "/guardar"),
		{
			codinf: $("#codinf").val(),
			email: $("#email").val(),
			nota: $("#nota").val(),
			estado: $("#estado").val()
		},
		function(response) {
			if (response["flag"] == true) {
				Messages.display(response["msg"], "success");
				buscar();
				$("#capture-modal").modal("hide");
			} else {
				Messages.display(response["msg"], "error");
			}
		}
	);
}

function reporte(tipo) {
	window.location = Utils.getKumbiaURL($Kumbia.controller + "/reporte/" + tipo);
}

function validePk() {
	if ($("#codinf").val() == "") return;
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/validePk"),
		data: {
			codinf: $("#codinf").val()
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == false) {
			Messages.display(response["msg"], "warning");
			$("#codinf").val("");
			$("#codinf").focus().select();
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function nuevo() {
	$("#form :input").each(function(elem) {
		$(this).val("");
		$(this).attr("disabled", false);
	});
	actualizar_select();
	$("#capture-modal").modal();
	setTimeout("focus_nuevo()", 500);
}

function focus_nuevo() {
	$("#codinf").focus().select();
}

function focus_editar() {
	$("#nombre").focus().select();
}

function borrar(codinf) {
	swal
		.fire({
			title: "Esta seguro de borrar?",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn btn-success btn-fill",
			cancelButtonClass: "btn btn-danger btn-fill",
			confirmButtonText: "SI",
			cancelButtonText: "NO"
		})
		.then(result => {
			if (result.value) {
				var request = $.ajax({
					type: "POST",
					url: Utils.getKumbiaURL($Kumbia.controller + "/borrar"),
					data: {
						codinf: codinf
					}
				});
				request.done(function(transport) {
					var response = transport;
					if (response["flag"] == true) {
						buscar();
						Messages.display(response["msg"], "success");
					} else {
						Messages.display(response["msg"], "error");
					}
				});
				request.fail(function(jqXHR, textStatus) {
					Messages.display(jqXHR.statusText, "error");
				});
			}
		});
}

function editar(codinf) {
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/editar"),
		data: {
			codinf: codinf
		}
	});
	request.done(function(transport) {
		var response = transport;
		$.each(response, function(key, value) {
			console.log(key, value);
			if (key != "archivo") $("#" + key).val(value);
		});
		$("#codinf").attr("disabled", true);
		$("#capture-modal").modal();
		setTimeout("focus_editar()", 500);
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function ir_servicios(codinf) {
	window.location = Utils.getKumbiaURL("mercurio59/index?codinf=" + codinf);
}
