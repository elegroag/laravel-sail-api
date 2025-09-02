import { CollectionView } from '../Collections/CollectionView';
import { ModelView } from '@/Common/ModelView';

class DocumentsCollectionView extends CollectionView {
	constructor(options = {}) {
		super(options);
		this.modelView = DocumentsRow;
	}

	get tagName() {
		return 'tbody';
	}

	/**
	 * @override
	 */
	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

class DocumentsRow extends ModelView {
	constructor(options = {}) {
		super(options);
		this.template = _.template(document.getElementById('tmp_docurow').innerHTML);
	}

	initialize() {
		this.listenTo(this.model, 'change', this.render);
	}

	get tagName() {
		return 'tr';
	}
}

export { DocumentsCollectionView };
