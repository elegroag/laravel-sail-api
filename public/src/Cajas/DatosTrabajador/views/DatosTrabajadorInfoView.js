import { $App } from '@/App';
import { FormInfoView } from '@/Cajas/FormInfoView';
import { is_numeric } from '@/Core';
import TrabajadorAprobarModel from '../../Trabajadores/models/TrabajadorAprobarModel';

export default class DatosTrabajadorInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			titulo: 'Aprobar datos trabajador',
			titulo_detalle: 'Lista datos trabajadores',
			Rules: TrabajadorAprobarModel.Rules,
			solicitudAprobar: options.collection.solicitud,
			camposDisponibles: options.collection.campos_disponibles,
		});
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(
			template({
				$scope: this.collection,
				model: this.model,
			}),
		);
		this.loadSubmenu();
		this.afterRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
			'click #aprobar_solicitud': 'aprobarSolicitud',
			'click #devolver_solicitud': 'devolverSolicitud',
			'click #rechazar_solicitud': 'rechazarSolicitud',
			'change #tippag': 'valTippag',
			"click [data-toggle='adjunto']": 'verArchivo',
		};
	}

	afterRender() {
		this.__afterRender();
		this.model.set({
			vendedor: 'N',
			empleador: 'N',
			tippag: 'T',
			giro: 'N',
			codsuc: '001',
			codlis: '001',
		});
		this.actualizaForm();
		this.$el.find('.js-basic-multiple, #codind, #tipsoc, #tipapo').select2();
	}

	loadSubmenu() {
		this.__loadSubmenu();
	}

	aprobarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);
		let $err = 0;
		let _numcue = $('#numcue').val();
		switch ($('#tippag').val()) {
			case 'A':
			case 'D':
				$('#tipcue').rules('add', { required: true });
				$('#banco').rules('add', { required: true });
				if (!is_numeric(_numcue)) {
					$('#numcue-error').text('Debe tener formato de número de cuenta correcto.');
					$('#numcue-error').attr('style', 'display:inline-block');
					$err++;
				}
				break;
			case 'C':
				$('#codban').rules('add', { required: false });
				break;
		}

		if (!this.form.valid()) {
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		_target.attr('disabled', true);
		const entity = this.serializeModel(this.model);

		$App.trigger('syncro', {
			url: 'validarMultiafiliacion',
			data: {
				id: this.collection.solicitud.get('id'),
			},
			callback: (response) => {
				if (response) {
					if (response.multi == true) {
						$App.trigger('confirma', {
							message:
								'El trabajador esta afiliado con otra empresa, desea registrar la actual ?',
							callback: (status) => {
								if (status) {
									this.__aprobar(_target, entity);
								}
							},
						});
					} else {
						this.__aprobar(_target, entity);
					}
				}
			},
		});
	}

	valTippag(e) {
		e.preventDefault();
		let tippag = $(e.currentTarget).val();
		if (tippag == '') return;

		$('#numcue').prop('disabled', false);
		$('#tipcue').prop('disabled', false);
		$('#numcue').attr('placeholder', '');

		switch (tippag) {
			case 'B':
				$('#numcue').prop('disabled', true);
				$('#tipcue').prop('disabled', true);

				$('#numcue').val('');
				$('#tipcue').val('');

				$('#codban').rules('add', { required: false });
				$('#codban').prop('disabled', true);
				break;
			case 'E':
				$('#numcue').prop('disabled', true);
				$('#tipcue').prop('disabled', true);

				$('#numcue').val('');
				$('#tipcue').val('');

				$('#codban').rules('add', { required: false });
				$('#codban').prop('disabled', true);
				break;
			case 'T':
				$('#numcue').prop('disabled', true);
				$('#tipcue').prop('disabled', true);

				$('#numcue').val('');
				$('#tipcue').val('');

				$('#codban').rules('add', { required: false });
				$('#codban').prop('disabled', true);

				break;
			case 'A':
				$('#codban').removeAttr('disabled');
				$('#codban').rules('add', { required: true });
				break;
			case 'D':
				$('#numcue').removeAttr('disabled');
				$('#codban').val('51');
				$('#tipcue').val('A');
				$('#numcue').attr('placeholder', 'Número teléfono certificado');
				$('#numcue').rules('add', { required: true });
				$('#codban').rules('add', { required: true });
				break;
		}
	}
}
