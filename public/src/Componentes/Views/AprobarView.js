import { ModelView } from '@/Common/ModelView';

class AprobarView extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_aprobar').innerHTML);
	}

	get className() {
		return 'col-auto';
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { AprobarView };
