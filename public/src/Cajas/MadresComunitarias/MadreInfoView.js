import { FormInfoView } from '@/Cajas/FormInfoView';
import IndependienteAprobarModel from '../Independientes/models/IndependienteAprobarModel';
import { $App } from '@/App';

class MadreInfoView extends FormInfoView {
	constructor(options = {}) {
		super({
			...options,
			titulo: 'Aprobar madre comunitaria',
			titulo_detalle: 'Lista madres comunitarias',
			Rules: IndependienteAprobarModel.Rules,
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
			"click [data-toggle='adjunto']": 'verArchivo',
		};
	}

	afterRender() {
		this.__afterRender();
		this.model.set({
			codind: '03',
			todmes: 'S',
			forpre: 'S',
			tipemp: 'P',
			ofiafi: '01',
			colegio: 'N',
			contratista: 'N',
			tipdur: 'S',
			pymes: 'N',
			tipapo: 'P',
		});

		this.actualizaForm();
		this.$el.find('.js-basic-multiple, #codind, #tipsoc, #tipapo').select2();
	}

	loadSubmenu() {
		this.__loadSubmenu();
		this.listenTo(this.headerView, 'load:aportes', this.aportesEmpresa);
	}

	aprobarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);
		if (!this.form.valid()) {
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		_target.attr('disabled', true);
		const entity = this.serializeModel(this.model);

		this.trigger('load:aprobar', {
			data: entity.toJSON(),
			callback: (response) => {
				_target.removeAttr('disabled');
				if (response.success) {
					$App.trigger('confirma', {
						message: response.msj,
						callback: (status) => {
							if (status) {
								this.remove();
								$App.router.navigate('list', { trigger: true, replace: true });
							}
						},
					});
				} else {
					Swal.fire({
						title: 'Notificación',
						text: response.msj,
						icon: 'error',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
				}
			},
		});
	}

	aportesEmpresa(data) {
		this.remove();
		$App.router.navigate('aportes/' + data.id, { trigger: true });
	}
}

export { MadreInfoView };
