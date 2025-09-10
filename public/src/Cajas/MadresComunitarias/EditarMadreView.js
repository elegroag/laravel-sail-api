import { Utils, $Kumbia } from '@/Utils';

class EditarMadreView extends Backbone.View {
	constructor(options) {
		super(options);
	}

	get className() {
		return 'col';
	}

	initialize() {
		this.template = document.getElementById('tmp_editar').innerHTML;
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(template(this.collection));
		this.__afterRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
		};
	}

	__afterRender() {
		var _formulario = $('#formulario_empresa');
		_formulario.validate({
			rules: {
				nit: {
					required: true,
					minlength: 6,
				},
				razsoc: {
					required: true,
					minlength: 6,
				},
				sigla: {
					required: false,
				},
				digver: {
					required: true,
					minlength: 1,
				},
				calemp: {
					required: true,
					minlength: 1,
				},
				cedrep: {
					required: true,
					minlength: 6,
				},
				repleg: {
					required: true,
					minlength: 10,
				},
				telefono: {
					required: true,
					minlength: 7,
				},
				celular: {
					required: true,
					minlength: 10,
				},
				fax: {
					required: false,
				},
				fecini: {
					required: true,
					minlength: 10,
				},
				tottra: {
					required: true,
				},
				valnom: {
					required: true,
					minlength: 6,
				},
				dirpri: {
					required: false,
				},
				ciupri: {
					required: false,
				},
				celpri: {
					required: false,
				},
				tipemp: {
					required: true,
				},
				tipper: {
					required: true,
				},
			},
			messages: {
				nombre: 'El campo es obligatorio.',
				email: 'Debe tener formato de email correcto.',
				telefono: 'El campo teléfono no contiene un formato correcto.',
				mensaje: 'El campo Mensaje es obligatorio',
				validator: 'Inerte los cuatro caracteres de la imagen superior.',
			},
		});

		$('#tipper').change(function (event) {
			if ($('#tipper').val() == 'N') {
				$('#show_natural').fadeIn('slow', function () {
					$('#show_juridica').fadeOut('fast');
				});
			} else {
				$('#show_juridica').fadeIn('slow', function () {
					$('#show_natural').fadeOut('fast');
				});
			}
		});

		var _selectores = $(
			'#tipdoc, #tipper, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg',
		);
		_selectores.select2();
		_selectores.trigger('change');
		$('#nit').attr('readonly', true);
	}

	guardarFicha(event) {
		event.preventDefault();
		let target = $(event.currentTarget);
		target.attr('disabled', true);

		let $err = 0;
		if (!_formulario.valid()) {
			$err++;
		}
		let _repleg;
		let _email = $('#email').val();
		let _emailpri = $('#emailpri').val();
		let _codact = $('#codact').val();
		let _codciu = $('#codciu').val();
		let _codzon = $('#codzon').val();
		let _tipsoc = $('#tipsoc').val();
		let _tipper = $('#tipper').val();
		let _tipdoc = $('#tipdoc').val();
		let _coddocrepleg = $('#coddocrepleg').val();

		if (_tipper == 'N') {
			_repleg =
				$('#priape').val() +
				' ' +
				$('#segape').val() +
				' ' +
				$('#prinom').val() +
				' ' +
				$('#segnom').val();
			if ($('#priape').val() == '') {
				$('#priape-error').text('El primer apellido del representante es requerido.');
				$('#priape-error').attr('style', 'display:inline-block');
				$err++;
			}
			if ($('#prinom').val() == '') {
				$('#prinom-error').text('El primer nombre del representante es requerido.');
				$('#prinom-error').attr('style', 'display:inline-block');
				$err++;
			}
		} else {
			_repleg =
				$('#priaperepleg').val() +
				' ' +
				$('#segaperepleg').val() +
				' ' +
				$('#prinomrepleg').val() +
				' ' +
				$('#segnomrepleg').val();
			if ($('#priaperepleg').val() == '') {
				$('#priaperepleg-error').text(
					'El primer apellido del representante es requerido.',
				);
				$('#priaperepleg-error').attr('style', 'display:inline-block');
				$err++;
			}
			if ($('#prinomrepleg').val() == '') {
				$('#prinomrepleg-error').text('El primer nombre del representante es requerido.');
				$('#prinomrepleg-error').attr('style', 'display:inline-block');
				$err++;
			}
		}
		if (!is_email(_email)) {
			$('#email-error').text('Debe tener formato de email correcto.');
			$('#email-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_emailpri != '') {
			if (!is_email(_emailpri)) {
				$('#emailpri-error').text('Debe tener formato de email correcto.');
				$('#emailpri-error').attr('style', 'display:inline-block');
				$err++;
			}
		}
		if (_codact == '' || _codact == '0000') {
			$('#codact-error').text('El código de la actividad económica es requerido.');
			$('#codact-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_codciu == '' || _codciu == '0000') {
			$('#codciu-error').text('La ciudad de notificación es requerida.');
			$('#codciu-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_codzon == '' || _codzon == '0000') {
			$('#codzon-error').text('La ciudad labor de trabajadores es requerida.');
			$('#codzon-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_tipsoc == '' || _tipsoc == '0') {
			$('#tipsoc-error').text('El tipo de sociedad es requerido.');
			$('#tipsoc-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_tipper == '') {
			$('#tipper-error').text('El tipo persona es requerido.');
			$('#tipper-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_tipdoc == '') {
			$('#tipdoc-error').text('El tipo documento es requerido.');
			$('#tipdoc-error').attr('style', 'display:inline-block');
			$err++;
		}
		if (_coddocrepleg == '') {
			$('#coddocrepleg-error').text('El tipo documento es requerido.');
			$('#coddocrepleg-error').attr('style', 'display:inline-block');
			$err++;
		}
		if ($err > 0) {
			Swal.fire({
				title: 'Notificación',
				text: 'Se requiere de resolver los campos requeridos para continuar.',
				icon: 'success',
				showConfirmButton: false,
				timer: 10000,
			});

			setTimeout(function () {
				$('label.error').text('');
				target.removeAttr('disabled');
			}, 4000);
			return false;
		}
		$('#repleg').val(_repleg);
		let _data_array = _formulario.serializeArray();
		let _token = {};
		let $i = 0;
		while ($i < _.size(_data_array)) {
			_token[_data_array[$i].name] = _data_array[$i].value;
			$i++;
		}

		let _url = Utils.getKumbiaURL($Kumbia.controller + '/edita_empresa');
		$.ajax({
			url: _url,
			method: 'POST',
			dataType: 'JSON',
			cache: false,
			data: _token,
		})
			.done(function (response) {
				target.removeAttr('disabled');
				if (response.success) {
					Swal.fire({
						title: 'Notificación Ok',
						html: "<p style='font-size:1em' class='text-center'>" + response.msj + '</p>',
						icon: 'success',
						showConfirmButton: false,
						timer: 10000,
					});
				}
			})
			.fail(function (rqs) {
				target.removeAttr('disabled');
				Swal.fire({
					title: 'Notificación',
					text: rqs.responeText,
					icon: 'success',
					showConfirmButton: false,
					timer: 10000,
				});
			});
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { EditarMadreView };
