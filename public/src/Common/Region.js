export class Region {
	#currentView = null;
	#currentViews = null;
	#$el = null;

	constructor(options = {}) {
		_.extend(this, options);
		this.currentViews = new Array();
		this.el = options.el;
	}

	// Closes any active view and render a new one
	show(view) {
		this.closeView(this.currentView);
		this.currentView = view;
		this.openView(view);
	}

	html(el) {
		if (this.$el) this.$el.html(el);
	}

	append(view) {
		this.currentViews.push(view);
		this.ensureEl();
		view.render();
		this.$el.append(view.el);
		if (view.onShow) view.onShow();
	}

	closeView(view) {
		// Only remove the view when the remove function
		// is available
		if (view && view.remove) {
			view.remove();
		}
	}

	openView(view) {
		// Be sure that this.$el exists
		this.ensureEl();

		// Render the view on the this.$el element
		view.render();
		this.$el.html(view.el);

		// Callback when the view is in the DOM
		if (view.onShow) view.onShow();
	}

	// Create the this.$el attribute if do not exists
	ensureEl() {
		if (this.$el) return;
		this.$el = $(this.el);
	}

	// Close the Region and any view on it
	remove() {
		if (this.currentViews.length > 0) {
			this.currentViews.forEach((view) => {
				this.closeView(view);
			});
		}
		this.closeView(this.currentView);
	}
}
