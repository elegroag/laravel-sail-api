'use strict';

class CollectionView extends Backbone.View {
	modelView = void 0;

	constructor(options = {}) {
		super(options);
		this.children = [];
	}

	initialize() {
		this.listenTo(this.collection, 'add', this.modelAdded);
		this.listenTo(this.collection, 'remove', this.modelRemoved);
		this.listenTo(this.collection, 'reset', this.render);
	}

	modelAdded(model) {
		const view = this.renderModel(model);
		this.$el.append(view.$el);
	}

	// Close view of model when is removed from the collection
	modelRemoved(model) {
		if (!model) return;

		let view = this.children[model.cid];
		this.closeChildView(view);
	}

	render() {
		this.closeChildren();
		const html = this.collection.map((model) => {
			let view = this.renderModel(model);
			return view.$el;
		});

		this.$el.html(html);
		return this;
	}

	renderModel(model) {
		// Create a new view instance, modelView should be
		// redefined as a subclass of Backbone.View
		let view;
		if (this.modelView) {
			view = new this.modelView({ model: model });
		} else {
			throw new Error('Error no hay model view ha mostrar');
		}

		// Keep track of which view belongs to a model
		this.children[model.cid] = view;

		// Re-trigger all events in the children views, so that
		// you can listen events of the children views from the
		// collection view
		this.listenTo(view, 'all', (eventName) => {
			this.trigger('item:' + eventName, view, model);
		});

		view.render();
		return view;
	}

	// Called to close the collection view, should close
	// itself and all the live childrens

	remove() {
		Backbone.View.prototype.remove.call(this);
		this.closeChildren();
	}

	// Close all the live childrens
	closeChildren() {
		const children = this.children || {};
		_.each(children, (child) => this.closeChildView(child));
	}

	// Close a single children at time
	closeChildView(view) {
		// Ignore if view is not valid
		if (!view) return;

		// Call the remove function only if available
		if (_.isFunction(view.remove)) {
			view.remove();
		}

		// Remove event hanlders for the view
		this.stopListening(view);

		// Stop tracking the model-view relationship for the
		// closed view
		if (view.model) {
			this.children[view.model.cid] = undefined;
		}
	}
}

export { CollectionView };
