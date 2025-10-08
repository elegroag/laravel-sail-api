export class Controller {
	App = null;
	currentController = null;
	region = null;
	layout = null;

	constructor(options) {
		this.currentController = undefined;
		this.region = options.region;
		this.App = options.App || window.App;
	}

	startController(Controller) {
		if (this.currentController && this.currentController instanceof Controller) {
			return this.currentController;
		}

		if (this.currentController && this.currentController.destroy) {
			this.currentController.destroy();
		}

		this.currentController = new Controller({
			region: this.region,
			App: this.App,
		});
		return this.currentController;
	}
}
