import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const validatorInit = () => {
	validator = $("#form").validate({
		rules: {
			archivo: { required: true },
			url: { required: true }
		}
	});
};

$(() => {
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
	const modalImagen = new bootstrap.Modal(document.getElementById('modalImagen'));

	$("#modalImagen").on("show.bs.modal", function(e) {
		var image = $(e.relatedTarget).css("background-image");
		image = image.replace("url(", "").replace(")", "").replace(/\"/gi, "");
		$("#img_zoom").attr("src", image);
	});

	$(document).on({mouseenter: function(){
		$(this).css({
			outline: "0px solid #6EE0FF"
		})
		.stop()
		.animate({
				outlineWidth: "2px",
				outlineColor: "#6EE0FF"
			},200
		)
		},mouseleave: function() {
			$(this).stop().animate(
				{
					outlineWidth: "0px",
					outlineColor: "#037736"
				},
				150
		)}}, ".thumbnail"
	);

	const galeria = () => {
		window.App.trigger('ajax', {
			type: "POST",
			url: window.ServerController + "/galeria",
			callback: (response) => {
				const tpl = _.template(document.getElementById('tmp_galeria_item').innerHTML);
				$("#galeria").html(tpl({
					_collection: response
				}));
			},
			error: (response) => {
				Messages.display(response.error, "error");
			}
		});
	}

	galeria();

	$("[data-toggle='editar']").on("click", function() {
		const codapl = $(this).data("cid");

		window.App.trigger('ajax', {
			url: window.ServerController + "/editar",
			data: {
				codapl
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
		window.App.trigger('ajax', {
			type: "POST",
			url: window.App.url(window.ServerController + "/guardar"),
			data: new FormData($("#form")[0]),
			processData: false,
			contentType: false
		}).done(function(response) {
			if (response.flag) {
				Messages.display(response.msg, "success");
				$("#form :input").each(function(elem) {
					$(this).val("");
					$(this).attr("disabled", false);
				});
				galeria();
			} else {
				Messages.display(response.msg, "error");
			}
		}).fail(function(jqXHR, textStatus) {
			Messages.display(textStatus, "error");
		});
	});

	$(document).on('click', "[data-toggle='header-nuevo']", (e) => {
		e.preventDefault();
		modalCapture.show();
		const tpl = _.template(document.getElementById('tmp_form').innerHTML);
		$('#captureModalbody').html(tpl({
			codapl: '',
			archivo: '',
			url: ''
		}));

		$("#form :input").each(function(elem) {
			$(this).val("");
			$(this).attr("disabled", false);
		});
		validatorInit();
	});

	$('[data-toggle="borrar"]').on('click', function() {
		Swal.fire({
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
				const numpro = $(this).data("cid");
				window.App.trigger('ajax', {
					url: window.ServerController + "/borrar",
					data: {
						numpro
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

	$('[data-toggle="arriba"]').on('click', function(e) {
		e.stopPropagation();
		const numpro = $(this).data("cid");
		window.App.trigger('ajax', {
			url: window.ServerController + "/arriba",
			data: {
				numpro
			},
			callback: (response)=> {
				if (response) {
					Messages.display(response["msg"], "success");
					galeria();
				} else {
					Messages.display(response["msg"], "error");
				}
			},
			error: (response) => {
				Messages.display(response.error, "error");
			}
		});
	});

	$('[data-toggle="abajo"]').on('click', function(e) {
		e.stopPropagation();
		const numpro = $(this).data("cid");
		window.App.trigger('ajax', {
			url: window.ServerController + "/abajo",
			data: {
				numpro
			},
			callback: (response)=> {
				if (response) {
					Messages.display(response["msg"], "success");
					galeria();
				} else {
					Messages.display(response["msg"], "error");
				}
			},
			error: (response) => {
				Messages.display(response.error, "error");
			}
		});
	});

});
