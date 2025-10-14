$(document).ready(function() {
	validator = $("#form").validate({
		rules: {
			codser: { required: true },
			numero: { required: true },
			email: { required: true },
			nota: { required: true },
			precan: { required: true },
			autser: { required: true },
			consumo: { required: true },
			estado: { required: true },
			archivo: { required: true }
		}
	});

	$("#codser").blur(function() {
		validePk();
	});
	$("#codser").change(function() {
		traerApertura();
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
			codser: $("#codser").val(),
			numero: $("#numero").val(),
			nota: $("#nota").val(),
			email: $("#email").val(),
			precan: $("#precan").val(),
			consumo: $("#consumo").val(),
			autser: $("#autser").val(),
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
	if ($("#codser").val() == "") return;
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/validePk"),
		data: {
			codser: $("#codser").val()
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == false) {
			Messages.display(response["msg"], "warning");
			$("#codser").val("");
			$("#codser").focus().select();
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function nuevo() {
	$("#form :input").each(function(elem) {
		if (this.id != "codinf") {
			$(this).val("");
			$(this).attr("disabled", false);
		}
	});
	actualizar_select();
	$("#capture-modal").modal();
	setTimeout("focus_nuevo()", 500);
}

function focus_nuevo() {
	$("#codser").focus().select();
}

function focus_editar() {
	$("#email").focus().select();
}

function borrar(codinf, codser, numero) {
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
						codinf: codinf,
						codser: codser,
						numero: numero
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

function editar(codinf, codser, numero) {
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/editar"),
		data: {
			codinf: codinf,
			codser: codser,
			numero: numero
		}
	});
	request.done(function(transport) {
		var response = transport;
		$.each(response, function(key, value) {
			if (key != "archivo" && key != "codinf") $("#" + key).val(value);
			if (key == "codser") {
				traerApertura();
			}
		});
		$("#codser").attr("disabled", true);
		$("#capture-modal").modal();
		setTimeout("focus_editar()", 500);
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

function traerApertura() {
	if ($("#codser").val() == "") return;
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/traerApertura"),
		async: false,
		data: {
			codser: $("#codser").val()
		}
	});
	request.done(function(transport) {
		var response = transport;
		$("#td_apertura").html(response);
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}
