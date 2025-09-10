import { Utils } from '@/Utils';
import { $App } from '@/App';
import { HeaderInfoView } from '@/Cajas/HeaderInfoView';
import { DevolverView } from '@/Componentes/Views/DevolverView';
import { RechazarView } from '@/Componentes/Views/RechazarView';
import { AprobarView } from '@/Componentes/Views/AprobarView';
import { HeaderCajasView } from './HeaderCajasView';

class FormInfoView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.headerView = undefined;
		this.devolverView = undefined;
		this.rechazarView = undefined;
		this.headerMain = undefined;
		this.form = undefined;
		this.titulo = undefined;
		this.titulo_detalle = undefined;
		this.Rules = undefined;
		this.solicitudAprobar = undefined;
		this.camposDisponibles = undefined;
		_.extend(this, options);
	}

	mestados = {
		'': 'Pendientes',
		P: 'Pendientes',
		R: 'Rechazadas',
		X: 'Rechazadas',
		A: 'Activas',
		I: 'Inactivas',
		D: 'Devueltas',
		T: 'Temporales',
	};

	get className() {
		return 'col';
	}

	initialize() {
		this.template = document.getElementById('tmp_info').innerHTML;
	}

	getInput(selector) {
		return this.$el.find(selector).val();
	}

	setInput(name, value) {
		return this.$el.find(`[name='${name}']`).val(value);
	}

	serializeModel(entity) {
		const dataArray = this.form.serializeArray();
		_.each(dataArray, (item) => entity.set(item.name, item.value));
		return entity;
	}

	actualizaForm() {
		_.each(this.model.toJSON(), (valor, key) => {
			if (_.isEmpty(valor) == true || _.isUndefined(valor) == true) {
			} else {
				let _type = this.$el.find(`[name='${key}']`).attr('type');
				if (_type === 'radio' || _type === 'checkbox') {
				} else {
					this.setInput(key, valor);
				}
			}
		});
	}

	rechazarSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);
		const _nota_rechazar = this.getInput('#nota_rechazar');
		const _codest_rechazar = this.getInput('#codest_rechazar');

		if (_nota_rechazar == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El valor de la nota es requerido para hacer la rechazar.',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		if (_codest_rechazar == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El estado es requerido para hacer rechazo',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		_target.attr('disabled', true);
		const _token = {
			id: this.solicitudAprobar.get('id'),
			nota: _nota_rechazar,
			codest: $('#codest').val(),
		};

		this.trigger('load:rechazar', {
			data: _token,
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

	devolverSolicitud(e) {
		e.preventDefault();
		const _target = this.$el.find(e.currentTarget);
		const _nota_devolver = this.getInput('#nota_devolver');
		const _codest_devolver = this.getInput('#codest_devolver');

		if (_nota_devolver == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El valor de la nota es requerido para hacer la devolución.',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		if (_codest_devolver == '') {
			Swal.fire({
				title: 'Notificación',
				text: 'El estado es requerido para hacer la devolución',
				icon: 'error',
				showConfirmButton: false,
				showCloseButton: true,
				timer: 10000,
			});
			return;
		}

		_target.attr('disabled', true);

		const _token = {
			id: this.solicitudAprobar.get('id'),
			nota: _nota_devolver,
			codest: _codest_devolver,
			campos_corregir: this.$el.find('#campos_corregir').val(),
		};

		this.trigger('load:devolver', {
			data: _token,
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

	__afterRender() {
		this.aprobarView = new AprobarView({
			model: {
				id: this.solicitudAprobar.get('id'),
				$scope: this.collection,
			},
		});

		this.$el.find('#renderAprobar').html(this.aprobarView.render().el);

		this.form = this.$el.find('#formAprobar');
		this.form.validate(this.Rules);
		this.devolverView = new DevolverView({
			model: {
				id: this.solicitudAprobar.get('id'),
				campos_disponibles: this.camposDisponibles,
			},
		});

		this.$el.find('#renderDevolver').html(this.devolverView.render().el);

		this.rechazarView = new RechazarView({
			model: {
				id: this.solicitudAprobar.get('id'),
			},
		});
		this.$el.find('#renderRechazar').html(this.rechazarView.render().el);

		this.$el.find('#nota_aprobar, #nota_rechazar, #nota_devolver').summernote({
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
	}

	__loadSubmenu(
		option = {
			deshacer: true,
			aportes: false,
			volver: true,
			editar: false,
			info: false,
			notificar: false,
		},
	) {
		const tipo_detalle = this.mestados[this.solicitudAprobar.get('estado')];

		this.headerMain = new HeaderCajasView({
			model: {
				titulo: this.titulo,
				detalle: this.titulo_detalle + ' - ' + tipo_detalle,
				info: false,
			},
		});

		$App.layout.getRegion('header').show(this.headerMain);

		const model = this.solicitudAprobar.toJSON();
		model.option = option;

		this.headerView = new HeaderInfoView({
			model: model,
		});

		this.listenTo(this.headerView, 'load:volver', this.__volverLista);
		this.listenTo(this.headerView, 'load:editar', this.__editarRequest);
		$App.layout.getRegion('subheader').show(this.headerView);
	}

	__volverLista() {
		this.remove();
		$App.router.navigate('list', { trigger: true, replace: true });
	}

	__editarRequest(data) {
		this.remove();
		$App.router.navigate('edit/' + data.id, { trigger: true });
	}

	__aprobar(_target, entity) {
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
					if (response.info && response.info.errors) {
						$.each(response.info.errors, (key, item) => {
							if (_.isArray(item) == true) {
								$.each(item, (key2, item2) => {
									$App.trigger('noty:error', item2);
								});
							} else {
								$App.trigger('noty:error', item);
							}
						});
					}

					$App.trigger('alert:warning', {
						message: response.msj,
					});
				}
			},
		});
	}

	verArchivo(e) {
		e.preventDefault();
		const target = $(e.currentTarget);
		const path = target.attr('data-path');
		const nomarc = target.attr('data-file');

		let _filepath;
		if (path != void 0 && nomarc != void 0) {
			_filepath = btoa(path + '' + nomarc);
		} else if (path != void 0 && nomarc == void 0) {
			_filepath = btoa(path);
		} else {
			return;
		}

		const _data = {
			url: Utils.getKumbiaURL('principal/download_global/' + _filepath),
			filename: _filepath,
		};

		$.ajax({
			type: 'POST',
			url: Utils.getKumbiaURL('principal/file_existe_global/' + _filepath),
			dataType: 'JSON',
			data: _data,
		}).done((resultado) => {
			if (resultado.success == true) {
				this.__winArchivo(path, nomarc);
			} else {
				Swal.fire({
					title: 'Notificación',
					text: 'El archivo no se logra localizar en el servidor',
					icon: 'warning',
					showConfirmButton: false,
					timer: 10000,
				});
			}
		});
	}

	__winArchivo(path = '', nomarc = '') {
		const url = ('../' + path + nomarc).replace('//', '/');
		window.open(
			Utils.getKumbiaURL(url),
			nomarc,
			'width=800, height=750,toobal=no,statusbar=no,scrollbars=yes menuvar=yes',
		);
	}

	deshacerSolicitud(e) {
		this.remove();
		const id = this.model.get('id');
		$App.router.navigate('deshacer/' + id, { trigger: true, replace: true });
	}

	remove() {
		if (this.headerView) this.headerView.remove();
		if (this.devolverView) this.devolverView.remove();
		if (this.rechazarView) this.rechazarView.remove();
		if (this.headerMain) this.headerMain.remove();
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { FormInfoView };
