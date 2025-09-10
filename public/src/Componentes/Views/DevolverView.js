import { ModelView } from '@/Common/ModelView';

class DevolverView extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_devolver').innerHTML);
	}

	get className() {
		return 'col-auto';
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { DevolverView };
