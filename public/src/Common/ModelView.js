export class ModelView extends Backbone.View {
	modelDOM = null;

	constructor(options = { modelDOM: null, onRender: null }) {
		super(options);
		this.modelDOM = options.modelDOM || Backbone.Model;
		this.template = null;
		_.extend(this, options);
		this.onRender = _.isFunction(this.onRender) ? this.onRender : null;
	}

	/**
	 * @override
	 */
	render() {
		const data = this.serializeData();
		let renderedHtml;

		if (_.isFunction(this.template)) {
			renderedHtml = this.template(data);
		} else if (_.isString(this.template)) {
			const compiledTemplate = this.compileTemplate();
			renderedHtml = compiledTemplate(data);
		}

		this.$el.html(renderedHtml);
		if (this.onRender) this.onRender(this.$el);
		return this;
	}

	compileTemplate() {
		if (_.isString(this.template) === true) {
			const _el = document.querySelector(this.template);
			return _.template(_el.innerHTML);
		}
		return false;
	}

	// Transform Model into JSON representation
	serializeData() {
		let data = null;

		// Only when model is available
		if (this.modelDOM !== null) {
			if (this.model instanceof this.modelDOM) {
				data = this.model.toJSON();
			} else {
				if (typeof this.model == 'object') {
					data = this.model;
				}
			}
		} else {
			if (typeof this.model == 'object') {
				data = this.model;
			}
		}
		return data ? data : {};
	}

	getInput(selector) {
		return this.$el.find(`[name='${selector}']`).val();
	}

	getInputById(id) {
		return this.$el.find('#' + id).val();
	}

	setInput(selector, val) {
		return this.$el.find(`[name='${selector}']`).val(val ?? '');
	}

	getInputByTag(tag, key) {
		return this.$el.find(`[${tag}='${key}']`).val();
	}

	getCheck(selector) {
		return this.$el.find(`[name='${selector}']:checked`).length;
	}

	getInputFile(id) {
		return this.$el.find('#' + id).files[0];
	}
}
