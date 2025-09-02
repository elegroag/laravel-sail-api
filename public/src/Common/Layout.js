import { ModelView } from '@/Common/ModelView';
import { Region } from './Region';

export class Layout extends ModelView {
	tagRegions = {};
	regionsLayout = {};

	constructor(options = {}) {
		super(options);
		_.extend(this, options);
	}

	/**
	 * @override
	 */
	render() {
		this.closeRegions();
		const result = ModelView.prototype.render.call(this);
		this.configureRegions();
		return result;
	}

	configureRegions() {
		const regionDefinitions = this.tagRegions || {};
		if (!this.regionsLayout) this.regionsLayout = {};

		_.each(regionDefinitions, (__selector, name) => {
			let $el = this.$(__selector);
			this.regionsLayout[name] = new Region({ el: $el });
		});
	}

	// Get a Region instance for a named region
	getRegion(regionName) {
		if (!this.regionsLayout) return false;
		return this.regionsLayout[regionName];
	}

	/**
	 * @override
	 */
	remove(options = {}) {
		this.stopListening();
		this.closeRegions();
		ModelView.prototype.remove.call(this, options);
	}

	closeRegions() {
		if (!this.regionsLayout) return false;
		_.each(this.regionsLayout, (region) => {
			if (region && region.remove) region.remove();
		});
	}
}
