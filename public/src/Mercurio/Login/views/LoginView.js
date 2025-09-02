import { $App } from '@/App';
import { ComponentModel } from '@/Componentes/Models/ComponentModel';
import { InputComponent, SelectComponent } from '@/Componentes/Views/ComponentsView';
import { UserAuthModel } from '../models/UserAuthModel';
import Choices from 'choices.js';
import { ModelView } from '@/Common/ModelView';

export default class LoginView extends ModelView {
	#App = null;
	constructor(options = {}) {
		super({
			...options,
			onRender: () => this.renderAfter(),
		});
		this.form = null;
		this.template = _.template(document.querySelector('#tmp_login').innerHTML);
		this.#App = options.App || $App;
	}

	initialize() {
		this.children = [];
	}

	get className() {
		return 'card mb-2';
	}

	get events() {
		return {
			'click #mostrar_password': 'showPassword',
			'focusout #cedrep': 'validePk',
			'change [name="tipafi"]': 'changeTipo',
			'click #bt_autenticate': 'autenticate',
			'click #bt_recuperar_clave': 'renderRecoveryPassword',
			'click #bt_solicitar_clave': 'solicitaClave',
			'click #bt_usar_clave': 'solicitaClave',
			'click #bt_cambia_email': 'solicitaCambiaEmail',
		};
	}

	renderAfter() {
		const view = this.addComponent(
			new ComponentModel({
				name: 'coddoc',
				type: 'select',
				placeholder: 'coddoc',
				disabled: false,
				readonly: false,
				order: 0,
				target: 1,
				searchType: 'local',
				search: 'coddoc',
				className: 'js-choice',
			}),
		);
		this.$el.find('#component_coddoc').html(view.$el);

		const compones = this.addComponent(
			new ComponentModel({
				name: 'tipafi',
				type: 'select',
				placeholder: 'tipafi',
				disabled: false,
				readonly: false,
				order: 0,
				target: 1,
				searchType: 'local',
				search: 'tipafi',
				className: 'js-choice',
			}),
		);

		this.$el.find('#component_tipafi').html(compones.$el);
		this.form = this.$el.find('#formLogin');
		this.form.validate({
			...UserAuthModel.Rules,
			highlight: function (element) {
				$(element).removeClass('is-valid').addClass('is-invalid');
			},
			unhighlight: function (element) {
				$(element).removeClass('is-invalid').addClass('is-valid');
			},
		});

		const Elements = this.$el.find('.js-choice');
		$.each(Elements, (key, element) => {
			new Choices(element);
		});

		return this;
	}

	renderRecoveryPassword(e) {
		e.preventDefault();
		this.remove();
		this.#App.router.navigate('recovery', { trigger: true });
	}

	addComponent(model = {}) {
		let view;
		if (_.size(this.children) > 0) {
			if (_.indexOf(this.children, model.get('cid')) != -1) {
				view = this.children[model.get('cid')];
			}
		}
		if (!view) {
			switch (model.get('type')) {
				case 'select':
					view = new SelectComponent({
						model: model,
						collection: this.#App.Collections.formParams,
					});
					break;
				case 'input':
					view = new InputComponent({
						model: model,
					});
					break;
				default:
					break;
			}
			this.children[model.get('cid')] = view;
		}
		view.render();
		return view;
	}

	autenticate(e) {
		e.preventDefault();
		const target = this.$el.find(e.currentTarget);
		target.attr('disabled', 'true');

		let _err = 0;
		if (this.form.valid() == false) _err++;

		if (_err > 0) {
			target.removeAttr('disabled');
			this.#App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
			return false;
		}

		const entity = new UserAuthModel({
			documento: this.$el.find('#documento').val(),
			tipo: this.$el.find('#tipafi').val(),
			tipafi: this.$el.find('#tipafi').val(),
			clave: this.$el.find('#clave').val(),
			coddoc: this.$el.find('#coddoc').val(),
		});

		if (entity.isValid() === false) {
			target.removeAttr('disabled');
			this.#App.trigger('alert:warning', {
				message:
					'Todos los campos son requeridos para continuar el proceso: ' +
					entity.validationError.join(' '),
			});
			setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
			return false;
		}

		this.#App.trigger('syncro', {
			url: this.#App.url('autenticar'),
			data: entity.toJSON(),
			callback: (response) => {
				target.removeAttr('disabled');
				this.$el.find('#documento').val('');
				this.$el.find('#clave').val('');
				if (response) {
					if (response.success === true) {
						window.location.href = this.#App.kumbiaURL(response.location);
					} else {
						if (response.noAccess == 1) {
							this.#App.trigger('confirma', {
								message: response.msj,
								callback: (status) => {
									if (status) {
										window.location.href = this.#App.url('registro_empresa');
									}
								},
							});
						} else if (response.noAccess == 2) {
							const miTokenAuth = btoa(
								JSON.stringify({
									documento: response.documento,
									coddoc: response.coddoc,
									tipo: response.tipo,
									tipafi: response.tipafi,
									id: response.id,
								}),
							);
							sessionStorage.setItem('miTokenAuth', miTokenAuth);
							this.#App.router.navigate('verification', {
								trigger: true,
								replace: true,
							});
						} else {
							this.#App.trigger('alert:warning', {
								message: response.msj,
								timer: 30000,
							});
							this.$el.find('#bt_usar_clave').trigger('click');
						}
					}
					return false;
				} else {
					return false;
				}
			},
		});
	}

	solicitaClave(e) {
		e.preventDefault();
		const has = $(e.currentTarget).attr('data-has');
		if (has == 'N') {
			this.$el.find('.inbox-clave').fadeOut(() => {
				this.$el.find('#bt_autenticate').text('Consultar afiliación');
				this.$el.find('#clave').val('xxxx');
				this.$el.find('.inuse-clave').fadeIn();
			});
		} else {
			this.$el.find('#clave').val('');
			this.$el.find('.inbox-clave').fadeIn(() => {
				this.$el.find('#bt_autenticate').text('Iniciar sesión');
				this.$el.find('.inuse-clave').fadeOut();
			});
		}
		return false;
	}

	showPassword(e) {
		let target = this.$el.find(e.currentTarget);

		if (target.hasClass('eye')) {
			target.html('<i class="fas fa-eye-slash"></i>');
			target.removeClass('eye');
			this.$el.find('#clave').attr('type', 'text');
		} else {
			target.html('<i class="fas fa-eye"></i>');
			target.addClass('eye');
			this.$el.find('#clave').attr('type', 'password');
		}
		return false;
	}

	changeTipo(e) {
		e.preventDefault();
		switch (this.$el.find(e.currentTarget).val()) {
			case 'E':
				this.$el.find('#lb_documento').text('Documento empleador');
				break;
			case 'I':
			case 'O':
			case 'F':
			case 'S':
			case 'T':
				this.$el.find('#lb_documento').text('Identificación trabajador');
				break;
			default:
				$('#lb_documento').text('Identificación');
				break;
		}
	}

	closeChildren() {
		const children = this.children || {};
		_.each(children, (child) => this.closeChildView(child));
	}

	closeChildView(view) {
		if (!view) return;
		if (_.isFunction(view.remove)) {
			view.remove();
		}
		this.stopListening(view);
		if (view.model) {
			this.children[view.model.cid] = undefined;
		}
	}

	solicitaCambiaEmail(e) {
		e.preventDefault();
		this.remove();
		this.#App.router.navigate('email_change', { trigger: true });
	}

	remove() {
		this.stopListening();
		this.closeChildren();
		ModelView.prototype.remove.call(this);
	}
}
