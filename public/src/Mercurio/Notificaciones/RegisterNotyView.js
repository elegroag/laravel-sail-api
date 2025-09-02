import { $App } from '@/App';
import { ModelView } from '../../Common/ModelView';

export default class RegisterNotyView extends ModelView {
	#App = null;
	constructor(options = {}) {
		super({
			...options,
			className: 'row',
			onRender: () => this.__afterRender(),
		});
		this.template = _.template(document.getElementById('tmp_formulario').innerHTML);
		this.#App = options.App || $App;
	}

	get events() {
		return {
			'click #btEnviarRegistro': 'sendNotify',
			'keydown #nota': 'keydownNoty',
			'keydown .note-editable': 'keydownNoty',
			'paste .note-editable': 'keydownNoty',
			'contextmenu .note-editable': 'keydownNoty',
		};
	}

	__afterRender() {
		this.$el.find('#nota').summernote({
			lang: 'es-ES',
			disableDragAndDrop: true,
			shortcuts: false,
			placeholder: 'Agregar los detalles a notificar',
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
			tabDisable: false,
			height: 100,
			lineHeights: ['1', '1.5'],
			callbacks: {
				onPaste: function (e) {
					e.preventDefault();
				},
				onKeyDown: function (e) {
					if (e.ctrlKey && e.keyCode === 86) {
						e.preventDefault();
					}
				},
				onChange: (contents = '') => {
					const cadenaModificada = contents.replace(/'/g, '"');
					this.$el.find('#nota').val(cadenaModificada);
				},
			},
		});

		$.extend(true, $.summernote.lang, {
			'es-ES': {
				examplePlugin: {
					exampleText: 'Ejemplo Texto',
					dialogTitle: 'Ejemplo Plugin',
					okButton: 'OK',
				},
			},
		});
	}

	sendNotify(e) {
		e.preventDefault();
		let target = this.$el.find(e.currentTarget);
		target.attr('disabled', true);

		let $err = 0;
		const servicio = this.$el.find('#servicio').val();
		const telefono = this.$el.find('#telefono').val();
		const nota = this.$el.find('#nota').summernote('code');
		const novedad = this.$el.find('#novedad').val();
		const archivo = document.getElementById('archivo').files;

		if (nota == '') {
			this.$el.find('#nota-error').text('La notificación es requerida.');
			this.$el.find('#nota-error').attr('style', 'display:inline-block');
			$err++;
		}

		if (telefono == '') {
			this.$el
				.find('#telefono-error')
				.text('El teléfono o celular de contacto es un valor requerido.');
			this.$el.find('#telefono-error').attr('style', 'display:inline-block');
			$err++;
		}

		if (archivo.length > 0) {
			if (!this._handleFile('archivo')) {
				this.$el
					.find('#archivo-error')
					.text(
						'El archivo no posee un formato valido, solo se admiten: imagenes, word o pdf',
					);
				this.$el.find('#archivo-error').attr('style', 'display:inline-block');
				$err++;
			}
		}

		if ($err > 0) {
			target.removeAttr('disabled');
			this.#App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => {
				$('label.error').text('');
			}, 10000);
			return false;
		}

		const _token = new FormData();
		_token.append('servicio', servicio);
		_token.append('telefono', telefono);
		_token.append('nota', nota);
		_token.append('novedad', novedad);
		if (archivo.length > 0) {
			_token.append('file', archivo[0]);
		} else {
			_token.append('file', false);
		}

		this.#App.trigger('confirma', {
			message: '¿Está seguro de envíar el reporte.?',
			callback: (status) => {
				if (status) {
					const url = this.#App.url('procesarNotificacion');
					this.#App.trigger('upload', {
						url,
						data: _token,
						callback: (response) => {
							target.removeAttr('disabled');
							if (response && response.success === true) {
								this.#App.trigger('alert:success', {
									message: response.msj,
								});
							} else {
								this.#App.trigger('alert:error', {
									title: 'Notificación de error',
									message: response.msj,
								});
							}
							this.$el.find('#servicio').val('');
							this.$el.find('#telefono').val('');
							this.$el.find('#novedad').val('');
							this.$el.find('#archivo').val('');
							this.$el.find('#nota').val('');
							this.$el.find('#nota').summernote('code', '');
						},
					});
				} else {
					target.removeAttr('disabled');
				}
			},
		});
	}

	_handleFile(_file) {
		const fileInput = document.getElementById(_file);
		if (fileInput.files.length == 0) return false;
		const allowedExtensions = /(.*?)\.(docx|doc|pdf|jpeg|jpg|png|gif)$/i;
		const filePath = fileInput.files[0]['name'];
		if (!allowedExtensions.exec(filePath)) {
			fileInput.value = '';
			return false;
		} else {
			return true;
		}
	}

	keydownNoty(e) {
		if (e.ctrlKey && e.key === 'v') {
			e.preventDefault();
			this.#App.trigger('alert:warning', {
				message:
					'Disculpas seño@r usuario. No está disponible la opción de pagar contenido de una fuente no confiable. Por favor, redacta los hechos e incidentes presentados durante el proceso de gestión.',
			});
		}
	}
}
