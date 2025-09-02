export class CollectionView extends Backbone.View {
	/**
	 * @override
	 */
	initialize() {
		// Keep track of rendered items
		this.children = {};

		// Bind collection events to automatically insert
		// and remove items in the view
		this.listenTo(this.collection, 'add', this.modelAdded);
		this.listenTo(this.collection, 'remove', this.modelRemoved);
		this.listenTo(this.collection, 'reset', this.render);
	}

	// Render a model when is added to the collection
	modelAdded(model) {
		const view = this.renderModel(model);
		this.$el.append(view.$el);
	}

	// Close view of model when is removed from the collection
	modelRemoved(model) {
		if (!model) return;

		const view = this.children[model.cid];
		this.closeChildView(view);
	}

	/**
	 * @override
	 */
	render() {
		// Clean up any previous elements rendered
		this.closeChildren();

		// Render a view for each model in the collection
		const html = this.collection.map((model) => {
			let view = this.renderModel(model);
			return view.$el;
		});

		// Put the rendered items in the DOM
		this.$el.html(html);
		return this;
	}

	renderModel(model) {
		// Create a new view instance, modelView should be
		// redefined as a subclass of Backbone.View
		const view = new this.modelView({ model: model });

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

	/**
	 * @override
	 */
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
