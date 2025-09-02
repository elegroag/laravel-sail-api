const loading = (() => {
	let status,
		element,
		loader,
		template = void 0;

	const show = (out = false, options) => {
		if (out) {
			element = document.createElement('div');
			element.setAttribute('id', 'loading_msj');
			$(element).html(
				"<div class='loading_msj'><p class='text-warning'>Procesando datos de busqueda...</p></div>",
			);
			document.getElementById('app').appendChild(element);
		}
		if (!status) {
			template = _.template($('#tmp_loader').html());
			loader = document.createElement('div');
			loader.setAttribute('class', 'loader');
			loader.setAttribute('id', 'loader');
			$(loader).append(template());
			document.body.appendChild(loader);
			loader.setAttribute('style', 'display:block');
			if (_.isObject(options) === true) {
				if (options.addClass) {
					$('.loader').addClass(options.addClass);
				}
			}
		}
		status = true;
	};

	const hide = (out = false) => {
		if (out) {
			element.remove();
		}
		if (status) {
			loader.remove();
		}
		status = void 0;
	};

	return {
		hide,
		show,
	};
})();

export default loading;
