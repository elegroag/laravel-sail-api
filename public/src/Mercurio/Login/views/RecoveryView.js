import Choices from 'choices.js';
import { $App } from '@/App';
import { ComponentModel } from '@/Componentes/Models/ComponentModel.js';
import { InputComponent, SelectComponent } from '@/Componentes/Views/ComponentsView.js';
import { UserRecoveryModel } from '../models/UserRecoveryModel.js';

export default class RecoveryView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.form = null;
		this.children = [];
	}

	get className() {
		return 'card mb-2';
	}

	get events() {
		return {
			'click #render_login_principal': 'renderLogin',
			'click #btn_recuperar_clave': 'recoveryPassword',
			'click #mostrar_password': 'showPassword',
			"change [name='tipo']": 'changeTipo',
		};
	}

	render() {
		const renderedHtml = _.template(document.querySelector('#tmp_recovery').innerHTML);
		this.$el.html(renderedHtml());

		let view = this.addComponent(
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

		view = this.addComponent(
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
			}),
		);

		this.$el.find('#component_tipafi').html(view.$el);

		const Elements = this.$el.find('.js-choice');
		$.each(Elements, (key, element) => {
			new Choices(element);
		});

		this.form = this.$el.find('#formRecovery');
		this.form.validate({
			...UserRecoveryModel.Rules,
			highlight: function (element) {
				$(element).removeClass('is-valid').addClass('is-invalid');
			},
			unhighlight: function (element) {
				$(element).removeClass('is-invalid').addClass('is-valid');
			},
		});
		return this;
	}

	renderLogin(e) {
		e.preventDefault();
		this.remove();
		$App.router.navigate('auth', { trigger: true });
	}

	recoveryPassword(e) {
		e.preventDefault();
		var target = this.$el.find(e.currentTarget);
		target.attr('disabled', 'true');

		let _err = 0;
		if (this.form.valid() == false) _err++;
		if (_err > 0) {
			target.removeAttr('disabled');
			$App.trigger('alert:warning', {
				message: 'Se requiere de resolver los campos requeridos para continuar.',
			});
			setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
			return false;
		}

		const entity = new UserRecoveryModel({
			documento: this.$el.find('#documento').val(),
			tipo: this.$el.find('#tipafi').val(),
			email: this.$el.find('#email').val(),
			coddoc: this.$el.find('#coddoc').val(),
		});

		if (entity.isValid() === false) {
			target.removeAttr('disabled');
			$App.trigger('alert:warning', {
				message:
					'Todos los campos son requeridos para continuar el proceso: <br/> ' +
					entity.validationError.join('<br/>'),
			});
			setTimeout(() => this.$el.find('label.error').fadeOut(), 6000);
			return false;
		}

		$App.trigger('syncro', {
			url: $App.url('recuperar_clave'),
			data: entity.toJSON(),
			callback: (response) => {
				target.removeAttr('disabled');
				this.$el.find('#documento').val('');
				this.$el.find('#email').val('');

				if (response) {
					this.$el.find('#email').val('');
					this.$el.find('#documento').val('');

					$App.router.navigate('auth', { trigger: true });
					if (response.success) {
						$App.trigger('alert:success', {
							message: response.msj,
						});
					} else {
						$App.trigger('alert:warning', {
							message: response.msj,
						});
					}
					return false;
				} else {
					$App.router.navigate('auth', { trigger: true });
					return false;
				}
			},
		});
	}

	showPassword(e) {
		let target = $(e.currentTarget);

		if (target.hasClass('eye')) {
			target.html('<i class="fas fa-eye-slash"></i>');
			target.removeClass('eye');
			$('#clave').attr('type', 'text');
		} else {
			target.html('<i class="fas fa-eye"></i>');
			target.addClass('eye');
			$('#clave').attr('type', 'password');
		}
		return false;
	}

	changeTipo(e) {
		let target = this.$el.find(e.currentTarget);
		let tipo = target.val();
		if (tipo == 'E') {
			this.$el.find('#lb_documento').text('Documento empleador');
		} else {
			this.$el.find('#lb_documento').text('Documento afiliado');
		}
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
						collection: $App.Collections.formParams,
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

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
		this.closeChildren();
	}

	closeChildren() {
		var children = this.children || {};
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
}
