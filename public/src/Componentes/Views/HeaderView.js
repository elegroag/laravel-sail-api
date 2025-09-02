import { ModelView } from '@/Common/ModelView';
class HeaderView extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('tmp_card_header').innerHTML);
	}

	get className() {
		return 'row justify-content-center';
	}

	/**
	 * @override
	 */
	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { HeaderView };
