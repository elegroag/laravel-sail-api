import { $App } from '@/App';
import { Messages } from '@/Utils';
import { buscar, EventsPagination, validePk } from '../Glob/Glob';

window.App = $App;
let validator = undefined;

const validatorInit = () => {
	validator = $("#form").validate({
		rules: {
			codapl: { required: false },
			webser: { required: false },
			path: { required: false },
			urlonl: { required: false },
			puncom: { required: false }
		}
	});
};

$(() => {
	window.App.initialize();
	const modalCapture = new bootstrap.Modal(document.getElementById('captureModal'));
	EventsPagination();

	const focus_nuevo = () => {
		$("#codapl").focus().select();
	}

	const focus_editar = () => {
		$("#webser").focus().select();
	}

	$("#codapl").blur(function() {
		validePk();
	});

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
			codapl: '',
			webser: '',
			path: '',
			urlonl: '',
			puncom: ''
		}));

		$("#form :input").each(function(elem) {
			$(this).val("");
			$(this).attr("disabled", false);
		});
		setTimeout("focus_nuevo()", 500);
		validatorInit();
	});

});