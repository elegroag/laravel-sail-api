$(document).ready(function() {
	validator = $("#form").validate({
		rules: {
			archivo: { required: true }
		}
	});

	$("#modal_imagen").on("show.bs.modal", function(e) {
		var image = $(e.relatedTarget).css("background-image");
		image = image.replace("url(", "").replace(")", "").replace(/\"/gi, "");
		$("#img_zoom").attr("src", image);
	});

	$(document).on(
		{
			mouseenter: function() {
				$(this)
					.css({
						outline: "0px solid #6EE0FF"
					})
					.stop()
					.animate(
						{
							outlineWidth: "2px",
							outlineColor: "#6EE0FF"
						},
						200
					);
			},
			mouseleave: function() {
				$(this).stop().animate(
					{
						outlineWidth: "0px",
						outlineColor: "#037736"
					},
					150
				);
			}
		},
		".thumbnail"
	);

	galeria();
});

function guardar() {
	if (!$("#form").valid()) {
		return;
	}
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/guardar"),
		data: new FormData($("#form")[0]),
		processData: false,
		contentType: false
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == true) {
			Messages.display(response["msg"], "success");
			$("#form :input").each(function(elem) {
				$(this).val("");
				$(this).attr("disabled", false);
			});
			galeria();
		} else {
			Messages.display(response["msg"], "error");
		}
	});
	request.fail(function(jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function galeria() {
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/galeria")
	});
	request.done(function(transport) {
		var html = "";
		var response = transport;
		$.each(response, function(key, value) {
			html += '<div class="col-lg-3 col-md-4 col-xs-6 mb-3">';
			html +=
				'<div class="thumbnail" style="opacity: 1;background-image: url(\'' +
				value.archivo +
				'\');background-size: 100% 100%;border-top: solid 1px #e5e5e5;border-right: solid 2px #e5e5e5;border-bottom: solid 2px #e5e5e5;border-left: solid 1px #e5e5e5;border-color: #e5e5e5;cursor: zoom-in;" data-toggle="modal" data-target="#modal_imagen">';
			html +=
				'<button type="button" style="float: right;" class="btn btn-default btn-sm btn-icon-only rounded-circle mt-2" onclick="borrar(\'' +
				value.numero +
				'\',event)"><i class="fa fa-times"></i></button>';
			html +=
				'<div class="caption" style="background: rgba(108, 108, 108, 0.6); margin-top: 65%; text-align: center;">';
			html += '<h4 class="text-white">Imagen N°' + value.numero + "</h4>";
			html += '<p class="pb-2">';
			html +=
				'<button type="button" class="btn btn-icon-only btn-info" onclick="arriba(\'' +
				value.numero +
				'\',event)"><i class="fas fa-long-arrow-alt-left"></i></button> ';
			html +=
				'<button type="button" class="btn btn-icon-only btn-info" onclick="abajo(\'' +
				value.numero +
				'\',event)"><i class="fas fa-long-arrow-alt-right"></i></button> ';
			html += "</p>";
			html += "</div>";
			html += "</div>";
			html += "</div>";
		});
		$("#galeria").html(html);
	});
	request.fail(function(jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function arriba(numero, e) {
	e.stopPropagation();
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/arriba"),
		data: {
			numero: numero
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == true) {
			Messages.display(response["msg"], "success");
			galeria();
		} else {
			Messages.display(response["msg"], "error");
		}
	});
	request.fail(function(jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function abajo(numero, e) {
	e.stopPropagation();
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/abajo"),
		data: {
			numero: numero
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == true) {
			Messages.display(response["msg"], "success");
			galeria();
		} else {
			Messages.display(response["msg"], "error");
		}
	});
	request.fail(function(jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function borrar(numero, e) {
	e.stopPropagation();
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
						numero: numero
					}
				});
				request.done(function(transport) {
					var response = transport;
					if (response["flag"] == true) {
						Messages.display(response["msg"], "success");
						galeria();
					} else {
						Messages.display(response["msg"], "error");
					}
				});
				request.fail(function(jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
			}
		});
}
