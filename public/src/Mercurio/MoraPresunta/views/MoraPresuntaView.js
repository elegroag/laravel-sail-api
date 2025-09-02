import { ModelView } from '@/Common/ModelView';
import ErrorHandler from '@/Common/ErrorHandler';
import Logger from '@/Common/Logger';

export default class MoraPresuntaView extends ModelView {
	#errorHandler;
	logger;

	constructor(options = {}) {
		super({
			...options,
			modelDOM: Backbone.Model,
		});

		this.logger = new Logger();
		this.#errorHandler = options.errorHandler || new ErrorHandler();
		this.template = _.template(document.getElementById('tmp_mora_presunta').innerHTML);
	}
}
