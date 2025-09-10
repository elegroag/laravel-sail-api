import { ModelView } from '@/Common/ModelView';
import { $Kumbia, Utils } from '@/Utils';

class EditarConyugeView extends ModelView {
	constructor(options) {
		super(options);
		this.template = document.getElementById('tmp_editar').innerHTML;
	}

	get className() {
		return 'col';
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
				cedcon: {
					required: true,
				},
				tipdoc: {
					required: true,
				},
				priape: {
					required: true,
				},
				prinom: {
					required: true,
				},
				fecnac: {
					required: true,
				},
				ciunac: {
					required: true,
				},
				sexo: {
					required: true,
				},
				estciv: {
					required: true,
				},
				comper: {
					required: true,
				},
				codciu: {
					required: true,
				},
				codzon: {
					required: true,
				},
				tipviv: {
					required: true,
				},
				telefono: {
					required: true,
				},
				celular: {
					required: true,
				},
				email: {
					required: true,
					email: true,
				},
				nivedu: {
					required: true,
				},
				fecing: {
					required: true,
				},
				codocu: {
					required: true,
				},
				salario: {
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

		var _selectores = $('#tipdoc, #codzon, #codciu, #tipafi');

		_selectores.select2();
		_selectores.trigger('change');
		$('#nit').attr('readonly', true);
	}

	guardarFicha(event) {
		event.preventDefault();
		let $err = 0;
		if (!_formulario.valid()) {
			$err++;
		}
		let _email = $('#email').val();
		let _codciu = $('#codciu').val();
		let _codzon = $('#codzon').val();

		if (!is_email(_email)) {
			$('#email-error').text('Debe tener formato de email correcto.');
			$('#email-error').attr('style', 'display:inline-block');
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
		if ($err > 0) {
			Swal.fire({
				title: 'Notificación',
				text: 'Se requiere de resolver los campos requeridos para continuar.',
				icon: 'success',
				showCancelButton: false,
				showCloseButton: false,
				showConfirmButton: false,
				timer: 10000,
			});
			setTimeout(function () {
				$('.error').text('');
			}, 10000);
			return false;
		}

		let _data_array = _formulario.serializeArray();
		let _token = {};
		let $i = 0;
		while ($i < _.size(_data_array)) {
			_token[_data_array[$i].name] = _data_array[$i].value;
			$i++;
		}

		_token['id'] = _ID;
		_token['cedcon'] = _SOLIICTUD.cedcon;
		_token['cedtra'] = _SOLIICTUD.cedtra;

		$.ajax({
			url: Utils.getKumbiaURL($Kumbia.controller + '/editar_solicitud'),
			method: 'POST',
			dataType: 'JSON',
			cache: false,
			data: _token,
		})
			.done(function (response) {
				if (response.success) {
					Swal.fire({
						title: 'Ok',
						html: '<p style="font-size:1em" class="text-center">' + response.msj + '</p>',
						icon: 'success',
						showConfirmButton: false,
						timer: 10000,
					});
				} else {
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'danger',
						showConfirmButton: false,
						timer: 10000,
					});
				}
			})
			.fail(function (rqs) {
				Swal.fire({
					title: 'Notificación',
					text: rqs.responeText,
					icon: 'danger',
					showConfirmButton: false,
					timer: 10000,
				});
			});
		return false;
	}
}

export { EditarConyugeView };
