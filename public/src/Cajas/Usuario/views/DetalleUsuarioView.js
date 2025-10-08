import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import {
	DialogComponent,
	RadioComponent,
	SelectComponent,
	TextComponent,
} from '@/Componentes/Views/ComponentsView';
import { UsuarioModel } from '../models/UsuarioModel';

class DetalleUsuarioView extends Backbone.View {
	constructor(options) {
		super(options);
		this.viewComponents = [];
		this.children = {};
		this.form = void 0;
	}

	//@ts-ignore
	get className() {
		return 'mb-3';
	}

	initialize() {
		this.template = document.getElementById('tmp_detalle').innerHTML;
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(template(this.model.toJSON()));
		this.form = this.$el.find('#formRequest');

		if (this.model.get('isEdit') == 1) {
			_.each(this.collection, (component) => {
				const view = this.addComponent(
					new ComponentModel({
						disabled: false,
						readonly: false,
						order: 0,
						target: 1,
						searchType: 'local',
						...component,
						valor: this.model.get(component.name),
					}),
					component.type,
				);

				this.viewComponents.push(view);
				this.$el.find('#component_' + component.name).html(view.$el);
			});
		}

		this.form.validate({
			rules: UsuarioModel.Rules,
			messages: {
				nombre: 'El campo es obligatorio.',
				mensaje: 'El campo mensaje es obligatorio',
			},
		});
		return this;
	}

	get events() {
		return {
			'click #bt_editar': 'editarDatos',
			'click #bt_close': 'closeEdit',
			'click #bt_change_clave': 'changeClaveRender',
			'click #bt_nochange_clave': 'changeNoClaveRender',
			'click #bt_crea_clave': 'crearClave',
			'keyup #newclave2': 'changeClaveNew',
			'click #bt_guardar': 'saveFormData',
		};
	}

	editarDatos(e) {
		e.preventDefault();
		this.remove();
		const documento = this.model.get('documento');
		const tipo = this.model.get('tipo');
		const coddoc = this.model.get('coddoc');
		window.App.router.navigate('editar/' + documento + '/' + tipo + '/' + coddoc, {
			trigger: true,
		});
	}

	closeEdit(e) {
		e.preventDefault();
		window.App.trigger('confirma', {
			message: 'Confirma que desea salir del modo edición de Perfil.',
			callback: (status) => {
				if (status) {
					this.remove();
					window.App.router.navigate('list', { trigger: true });
				}
			},
		});
	}

	changeClaveRender(e) {
		e.preventDefault();
		this.$el.find('#show_change_clave').removeClass('d-none');
	}

	changeNoClaveRender(e) {
		e.preventDefault();
		this.$el.find('#show_change_clave').addClass('d-none');
		this.$el.find('#newclave1').val('');
		this.$el.find('#newclave2').val('');
	}

	changeClaveNew(e) {
		let target = this.$el.find(e.currentTarget);
		let valor = target.val();
		if (valor.length > 0) {
			let clave1 = this.$el.find('#newclave1').val();
			if (clave1 !== valor) {
				this.$el
					.find('#show_error_clave')
					.text('Error la clave no es igual a la anterior');
				this.$el.find('#show_error_clave').fadeIn();
			} else {
				this.$el.find('#show_error_clave').text('');
				this.$el.find('#show_error_clave').fadeOut();
			}
		}
	}

	crearClave(e) {
		e.preventDefault();
		const claveGenerada = this.__generarClaveAleatoria();
		this.$el.find('#newclave1').val(claveGenerada);
	}

	//@ts-ignore
	remove() {
		if (_.size(this.viewComponents) > 0)
			_.each(this.viewComponents, (view) => view.remove());
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}

	addComponent(model, type) {
		const collection = window.App.Collections.formParams;
		let view;
		if (_.size(this.children) > 0) {
			if (_.indexOf(this.children, model.get('cid')) != -1) {
				view = this.children[model.get('cid')];
			}
		}
		if (!view) {
			switch (type) {
				case 'select':
					view = new SelectComponent({ model, collection });
					break;
				case 'radio':
					view = new RadioComponent({ model, collection });
					break;
				case 'date':
					view = new RadioComponent({ model, collection });
					break;
				case 'text':
					view = new TextComponent({ model, collection });
					break;
				case 'dialog':
					view = new DialogComponent({ model, collection });
					break;
				default:
					break;
			}
			this.children[model.get('cid')] = view;
		}
		view.render();
		return view;
	}

	saveFormData(event) {
		event.preventDefault();
		var target = this.$el.find(event.currentTarget);
		target.attr('disabled', true);

		let _err = 0;
		if (this.form.valid() == false) _err++;

		if (_err > 0) {
			target.removeAttr('disabled');
			window.App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => this.$el.find('label.error').text(''), 6000);
			return false;
		}

		this.$el.find('#documento').removeAttr('disabled');
		this.$el.find('#clave').removeAttr('disabled');
		this.$el.find('#clave').removeClass('disabled');

		const entity = new UsuarioModel(this.model.toJSON());
		entity.set('old_coddoc', this.model.get('coddoc'));
		this.__serializeModel(entity);

		if (entity.isValid() !== true) {
			target.removeAttr('disabled');
			window.App.trigger('alert:warning', { message: entity.validationError.join('<br/>') });
			setTimeout(() => $('label.error').text(''), 6000);
			return false;
		}

		window.App.trigger('confirma', {
			message: 'Confirma que desea guardar los datos del formulario.',
			callback: (status) => {
				if (status) {
					this.trigger('form:save', {
						entity: entity,
						isNew: false,
						callback: (response) => {
							target.removeAttr('disabled');
							this.$el.find('#documento').attr('disabled', true);
							this.$el.find('#clave').attr('disabled', true);
							this.$el.find('#clave').addClass('disabled');

							if (response) {
								if (response.success) {
									this.model.set(response.data);

									Swal.fire({
										title: 'RESULTADO OK',
										text: 'Los cambios se han realizado con exito.',
										icon: 'success',
										showCancelButton: false,
										confirmButtonColor: '#2dce89',
										cancelButtonColor: '#fc8c72',
										confirmButtonText: 'SI, Continuar!',
									}).then((result) => {
										const documento = this.model.get('documento');
										const tipo = this.model.get('tipo');
										const coddoc = this.model.get('coddoc');
										this.remove();

										window.App.router.navigate(
											'detalle/' + documento + '/' + tipo + '/' + coddoc,
											{
												trigger: true,
											},
										);
									});
								} else {
									window.App.trigger('alert:error', { message: response.msj });
								}
							}
						},
					});
				} else {
					this.$el.find('#documento').attr('disabled', true);
					this.$el.find('#clave').attr('disabled', true);
					this.$el.find('#clave').addClass('disabled');
					target.removeAttr('disabled');
				}
			},
		});
	}

	__serializeModel(entity) {
		const dataArray = this.form.serializeArray();
		_.each(dataArray, (item) => entity.set(item.name, item.value));
		return entity;
	}

	__generarClaveAleatoria() {
		const caracteresPermitidos =
			'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789$&@*#';
		let clave = '';
		for (let i = 0; i < 8; i++) {
			const indiceAleatorio = Math.floor(Math.random() * caracteresPermitidos.length);
			clave += caracteresPermitidos.charAt(indiceAleatorio);
		}
		return clave;
	}
}

export { DetalleUsuarioView };
