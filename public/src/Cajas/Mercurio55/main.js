$(document).ready(function() {
	validator = $("#form").validate({
		rules: {
			codare: { required: true },
			detalle: { required: true },
			tipo: { required: true },
			codcat: { required: true },
			estado: { required: true }
		}
	});

	$("#codare").blur(function() {
		validePk();
	});

	$("#capture-modal").on("hide.bs.modal", function(e) {
		validator.resetForm();
		$(".select2-selection").removeClass(validator.settings.errorClass).removeClass(validator.settings.validClass);
	});
	aplicarFiltro();
});

function reporte(tipo) {
	window.location = Utils.getKumbiaURL($Kumbia.controller + "/reporte/" + tipo);
}

function validePk() {
	if ($("#codare").val() == "") return;
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/validePk"),
		data: {
			codare: $("#codare").val()
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == false) {
			Messages.display(response["msg"], "warning");
			$("#codare").val("");
			$("#codare").focus().select();
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function nuevo() {
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/nuevo")
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == false) {
			Messages.display(response["msg"], "warning");
		} else {
			$("#form :input").each(function(elem) {
				$(this).val("");
				$(this).attr("disabled", false);
			});
			actualizar_select();
			$("#capture-modal").modal();
			$("#codare").val(response.data);
			setTimeout("focus_nuevo()", 500);
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function focus_nuevo() {
	$("#detalle").focus().select();
}

function focus_editar() {
	$("#detalle").focus().select();
}

function borrar(codare) {
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
						codare: codare
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

function editar(codare) {
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/editar"),
		data: {
			codare: codare
		}
	});
	request.done(function(transport) {
		var response = transport;
		$.each(response, function(key, value) {
			$("#" + key).val(value);
		});
		$("#codare").attr("disabled", true);
		$("#capture-modal").modal();
		setTimeout("focus_editar()", 500);
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function guardar() {
	if (!$("#form").valid()) {
		return;
	}
	$("#form :input").each(function(elem) {
		$(this).attr("disabled", false);
	});
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/guardar"),
		data: $("#form").serialize()
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == true) {
			buscar();
			Messages.display(response["msg"], "success");
			$("#capture-modal").modal("hide");
		} else {
			Messages.display(response["msg"], "error");
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function irArchivos(codare) {
	window.location = Utils.getKumbiaURL("mercurio58/index/" + codare);
}
