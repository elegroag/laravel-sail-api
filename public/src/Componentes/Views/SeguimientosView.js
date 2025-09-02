class SeguimientosView extends Backbone.View {
	constructor(options) {
		super(options);
	}

	get events() {
		return {};
	}

	render() {
		const { campos_disponibles, estados_detalles, seguimientos } = this.collection[0];
		const template = _.template(document.getElementById('tmp_seguimientos').innerHTML);
		const renderedHtml = template({
			campos_disponibles,
			estados_detalles,
			seguimientos,
		});

		this.$el.html(renderedHtml);
		return this;
	}
}

export { SeguimientosView };
