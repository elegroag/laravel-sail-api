import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

export default class ReaprobarView extends ModelView {
	constructor(options) {
		super({
			...options,
			onRender: () => this.afterRender(),
			className: 'reaprobar-view',
		});
		this.template = _.template(document.getElementById('tmp_reaprobar').innerHTML);
	}

	get events() {
		return {
			'click #procesarReaprobar': 'reaprobarSolicitud',
		};
	}

	afterRender() {
		this.$el.find('#nota_reaprobar').summernote({
			lang: 'es-ES',
			placeholder: '',
			disableDragAndDrop: true,
			shortcuts: false,
			toolbar: [
				['style', ['bold', 'italic', 'underline', 'clear']],
				['color', ['color']],
				['fontname', ['fontname']],
				['fontsize', ['fontsize']],
				['insert', ['link']],
				['para', ['ul', 'ol', 'paragraph']],
				['view', ['fullscreen', 'codeview']],
				['table', ['table']],
			],
			tabsize: 2,
			height: 100,
			callbacks: {
				onPaste: function (e) {
					e.preventDefault();
				},
				onKeyDown: function (e) {
					if (e.ctrlKey && e.keyCode === 86) {
						e.preventDefault();
					}
				},
			},
		});

		if (this.model.tipo == 'T') {
			this.$el.find('#renderByTrabajador').fadeIn();
		} else {
			this.$el.find('#renderByTrabajador').fadeOut();
		}
	}

	reaprobarSolicitud(e) {
		e.preventDefault();
		let _target = this.$el.find(e.currentTarget);
		_target.attr('disabled', 'true');
		let $err = 0;
		let _nota_reaprobar = this.$el.find('#nota_reaprobar').val();
		if (_nota_reaprobar == '') {
			$err++;
			$App.trigger('alert:error', {
				title: 'Notificación Alerta',
				message: 'Digíte la nota',
			});

			_target.removeAttr('disabled');
			return false;
		}

		if ($err > 0) {
			$App.trigger('alert:error', {
				title: 'Notificación Alerta',
				message: 'Algunos campos son requeridos para continuar',
			});
			_target.removeAttr('disabled');
			return false;
		}

		$App.trigger('syncro', {
			url: $App.url('reaprobar'),
			data: {
				id: this.model.id,
				nota: _nota_reaprobar,
				giro: this.$el.find('#giro').val(),
				codgir: this.$el.find('#codgir').val(),
			},
			silent: true,
			callback: (response) => {
				if (response.success) {
					$App.trigger('alert:success', {
						title: 'Notificación',
						message: response.msj,
					});
					_target.removeAttr('disabled');
					setTimeout(function () {
						$('label.error').text('');
						_target.removeAttr('disabled');
					}, 5000);
					return false;
				} else {
					$App.trigger('alert:error', {
						title: 'Notificación Alerta',
						message: response.msj,
					});
					_target.removeAttr('disabled');
				}
			},
		});
	}
}
