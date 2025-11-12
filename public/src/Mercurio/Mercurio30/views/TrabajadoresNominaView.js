import { CollectionView } from '@/Componentes/Collections/CollectionView';
import { ModelView } from '@/Common/ModelView';

class TrabajadoresNominaView extends CollectionView {
	constructor(options) {
		super(options);
		this.modelView = TrabajadoresNominaRow;
	}

	get tagName() {
		return 'tbody';
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}

	get events() {
		return {
			'click [data-toggle="delete-tra"]': 'removeTrabaj',
		};
	}

	removeTrabaj(e) {
		e.preventDefault();
		var target = this.$el.find(e.currentTarget);
		const cedtra = target.attr('data-cid');
		this.collection.remove(cedtra);
	}
}

class TrabajadoresNominaRow extends ModelView {
	constructor(options) {
		super(options);
		this.template = _.template(document.getElementById('tmp_tranom').innerHTML);
	}

	initialize() {
		this.listenTo(this.model, 'change', this.render);
	}

	get tagName() {
		return 'tr';
	}
}

export { TrabajadoresNominaRow, TrabajadoresNominaView };
