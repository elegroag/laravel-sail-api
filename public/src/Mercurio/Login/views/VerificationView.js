import { $App } from '@/App';

export default class VerificationView extends Backbone.View {
	constructor(options = {}) {
		super(options);
		this.selectores = undefined;
		this.formulario = undefined;
		_.extend(this, options);
	}

	initialize() {}

	get className() {
		return 'row';
	}

	render() {
		const template = document.getElementById('tmp_verification').innerHTML;
		const renderedHtml = _.template(template);
		this.$el.html(renderedHtml());
		return this;
	}

	get events() {
		return {
			"keyup input[data-toggle='code']": 'goToNextInput',
			"keydown input[data-toggle='code']": 'onKeyDown',
			"click input[data-toggle='code']": 'onFocus',
			'click #btnVerify': 'verifyCode',
		};
	}

	goToNextInput(e = {}) {
		const key = e.which;
		const t = this.$el.find(e.target);
		let sib = t.next('input');
		const rf = _.union(_.range(48, 58), _.range(96, 106));
		if (key === 9) {
			return true;
		} else {
			if (_.contains(rf, key) === false) {
				e.preventDefault();
				return false;
			}
		}
		if (!sib || !sib.length) {
			sib = this.$el.find('input').eq(0);
		}
		if (sib.val() == '') sib.select().focus();
	}

	onKeyDown(e = {}) {
		const key = e.which;
		const rf = _.union(_.range(48, 58), _.range(96, 106));
		if (key === 9) {
			return true;
		} else {
			if (_.contains(rf, key) === true) {
				return true;
			}
		}
		e.preventDefault();
		return false;
	}

	onFocus(e) {
		$(e.target).select();
	}

	verifyCode(e) {
		e.preventDefault();
		const params = this.model;
		$App.trigger('syncro', {
			url: $App.url('tokenParticular'),
			data: params,
			callback: (response) => {
				if (response) {
					if (response.success) {
						const token = response.token;
						this.trigger('send:verify', {
							data: {
								code_1: this.getInput('code_1'),
								code_2: this.getInput('code_2'),
								code_3: this.getInput('code_3'),
								code_4: this.getInput('code_4'),
								...params,
							},
							token: token,
							callback: (response = {}) => {
								if (response.success == true) {
									if (response.isValid == true) {
										window.location.href = $App.kumbiaURL(
											'principal/ingresoDirigido/' + response.token,
										);
									} else {
										$App.trigger('alert:error', {
											message: response.msj,
										});
										this.setInput('code_1', '');
										this.setInput('code_2', '');
										this.setInput('code_3', '');
										this.setInput('code_4', '');
									}
								} else {
									$App.trigger('alert:error', { message: response.msj });
									this.setInput('code_1', '');
									this.setInput('code_2', '');
									this.setInput('code_3', '');
									this.setInput('code_4', '');
								}
							},
						});
					} else {
						$App.trigger('alert:error', { message: response.msj });
					}
				}
			},
		});
	}

	getInput(selector = '') {
		return this.$el.find(`[name='${selector}']`).val();
	}

	setInput(selector = '', value = '') {
		return this.$el.find(`[name='${selector}']`).val(value);
	}
}
