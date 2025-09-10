import { ModelView } from '@/Common/ModelView';

class RechazarView extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_rechazar').innerHTML);
	}

	get className() {
		return 'col-auto';
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { RechazarView };
