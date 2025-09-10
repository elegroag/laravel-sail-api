import { $App } from '@/App';
import { ModelView } from '@/Common/ModelView';

class HeaderListView extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_list_header').innerHTML);
	}

	get className() {
		return 'col-auto';
	}

	get events() {
		return {
			"click [data-toggle='link']": 'optLink',
			"click [data-toggle='excel']": 'exportarAction',
		};
	}

	exportarAction(e) {
		e.preventDefault();
		this.trigger('show:reporte');
	}

	optLink(e) {
		e.preventDefault();
		const tipo = this.$el.find(e.currentTarget).attr('data-tipo');
		const url =
			tipo != '' && tipo != null && tipo !== undefined ? 'list/' + tipo : 'list';
		$App.router.navigate(url, { trigger: true, replace: true });
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { HeaderListView };
