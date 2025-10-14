import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const validatorInit = () => {
	validator = $("#form").validate({
		rules: {
			codcat: { required: true },
			detalle: { required: true },
			tipo: { required: true },
			estado: { required: true }
		}
	});
};

const valideKey = () => {
	if ($("#codcat").val() == "") return;
	var request = $.ajax({
		type: "POST",
		url: Utils.getKumbiaURL($Kumbia.controller + "/validePk"),
		data: {
			codcat: $("#codcat").val()
		}
	});
	request.done(function(transport) {
		var response = transport;
		if (response["flag"] == false) {
			Messages.display(response["msg"], "warning");
			$("#codcat").val("");
			$("#codcat").focus().select();
		}
	});
	request.fail(function(jqXHR, textStatus) {
		Messages.display(jqXHR.statusText, "error");
	});
}

const reporte = (tipo) => {
	window.location.href = window.App.url(window.ServerController + "/reporte/" + tipo);
}

$(() => {
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
	EventsPagination();

	const focus_nuevo = () => {
		$("#detalle").focus().select();
	}

	const focus_editar = () => {
		$("#detalle").focus().select();
	}

	$("#codcat").blur(function() {
		valideKey();
	});

	$("[data-toggle='editar']").on("click", function() {
		const codcat = $(this).data("cid");

		window.App.trigger('ajax', {
			url: window.ServerController + "/editar",
			data: {
				codcat
			},
			callback: (response) => {
				if (response) {
					modalCapture.show();
					const tpl = _.template(document.getElementById('tmp_form').innerHTML);
                    $('#captureModalbody').html(tpl(response));

					$("#form :input").each(function(elem) {
						$(this).val("");
						$(this).attr("disabled", false);
					});
					$.each(response, function(key, value) {
						$("#" + key).val(value);
					});
					$("#codapl").attr("disabled", true);
					setTimeout("focus_editar()", 500);
					validatorInit();
				} else {
					Messages.display(response["msg"], "error");
				}
			},
			error: (response) => {
				Messages.display(response.error, "error");
			}
		});
	});

	$('[data-toggle="guardar"]').on('click', function() {
		if (!$("#form").valid()) {
			return;
		}
		$("#form :input").each(function(elem) {
			$(this).attr("disabled", false);
		});

		window.App.trigger('ajax', {
			url: window.ServerController + "/guardar",
			data: $("#form").serialize(),
			callback: (response) => {
				if (response) {
					buscar();
					Messages.display(response["msg"], "success");
					modalCapture.hide();
				} else {
					Messages.display(response["msg"], "error");
				}
			},
			error: (response) => {
				Messages.display(response.error, "error");
			}
		});
	});

	$(document).on('click', "[data-toggle='header-nuevo']", (e) => {
		e.preventDefault();
		modalCapture.show();
		const tpl = _.template(document.getElementById('tmp_form').innerHTML);
		$('#captureModalbody').html(tpl({
			codcat: '',
			detalle: '',
			tipo: '',
			estado: ''
		}));

		$("#form :input").each(function(elem) {
			$(this).val("");
			$(this).attr("disabled", false);
		});
		setTimeout("focus_nuevo()", 500);
		validatorInit();
	});

	$('[data-toggle="borrar"]').on('click', function() {
		const codcat = $(this).data("cid");
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
				window.App.trigger('ajax', {
					url: window.ServerController + "/borrar",
					data: {
						codcat: codcat
					},
					callback: (response)=> {
						if (response) {
							buscar();
							Messages.display(response["msg"], "success");
						} else {
							Messages.display(response["msg"], "error");
						}
					},
					error: (response) => {
						Messages.display(response.error, "error");
					}
				});
			}
		});
	});

});
