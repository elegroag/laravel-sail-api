import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';
import EmpresaModel from '../models/EmpresaModel';

export default class EditarEmpresaView extends ModelView {
	form = null;
	headerView = null;
	headerMain = null;

	constructor(options) {
		super({
			...options,
			onRender: () => this.__afterRender(),
			className: 'col',
		});
		this.template = _.template(document.getElementById('tmp_editar').innerHTML);
		this.form = null;
		this.headerView = null;
		this.headerMain = null;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
			'change #tipper': 'changeTipper',
			'click #guardar_ficha': 'guardarFicha',
		};
	}

	__afterRender() {
		_.each(this.model.toJSON(), (valor, key) => {
			if (_.isEmpty(valor) == true || _.isUndefined(valor) == true) {
			} else {
				let _type = this.$el.find(`[name='${key}']`).attr('type');
				if (_type === 'radio' || _type === 'checkbox') {
				} else {
					this.$el.find('#' + key).val(valor);
				}
			}
		});

		this.form = this.$el.find('#formEditar');
		this.form.validate({
			rules: EmpresaModel.Rules,
			messages: {
				nombre: 'El campo es obligatorio.',
				email: 'Debe tener formato de email correcto.',
				telefono: 'El campo teléfono no contiene un formato correcto.',
				mensaje: 'El campo Mensaje es obligatorio',
				validator: 'Inerte los cuatro caracteres de la imagen superior.',
			},
		});
		const _selectores = $(
			'#tipdoc, #tipper, #tipsoc, #ciupri, #codzon, #codciu, #codact, #coddocrepleg',
		);
		_selectores.select2();
		_selectores.trigger('change');
		this.$el.find('#nit').attr('readonly', true);
	}

	changeTipper(e) {
		e.preventDefault();
		if (this.$el.find('#tipper').val() == 'N') {
			this.$el.find('#show_natural').fadeIn('slow', function () {
				this.$el.find('#show_juridica').fadeOut('fast');
			});
		} else {
			this.$el.find('#show_juridica').fadeIn('slow', function () {
				this.$el.find('#show_natural').fadeOut('fast');
			});
		}
	}

	guardarFicha(event) {
		event.preventDefault();
		const target = $(event.currentTarget);
		target.attr('disabled', true);

		let $err = 0;
		if (!this.form.valid()) {
			$err++;
		}
		let _repleg;
		const _email = $('#email').val();
		const _emailpri = $('#emailpri').val();
		const _codact = $('#codact').val();
		const _codciu = $('#codciu').val();
		const _codzon = $('#codzon').val();
		const _tipsoc = $('#tipsoc').val();
		const _tipper = $('#tipper').val();
		const _tipdoc = $('#tipdoc').val();
		const _coddocrepleg = $('#coddocrepleg').val();

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
		const _data_array = this.form.serializeArray();

		let _token = {};
		let _i = 0;
		while (_i < _.size(_data_array)) {
			_token[_data_array[_i].name] = _data_array[_i].value;
			_i++;
		}

		$.ajax({
			url: $App.url('edita_empresa'),
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
}
