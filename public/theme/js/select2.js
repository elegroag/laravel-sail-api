'use strict';

/*
 * Wrapper de Select2 para el tema.
 *
 * Funciones:
 *   1. Asegura que `jQuery.fn.select2` esté disponible. Si el layout no carga
 *      `assets/select2/js/select2.full.min.js` por sí mismo (por ejemplo, el
 *      layout `auth.blade.php`), este script lo descarga en tiempo de ejecución.
 *   2. Inicializa Select2 sobre:
 *        - [data-toggle="select"]   -> selector genérico del tema.
 *        - .js-basic-multiple        -> selectores múltiples en formularios de
 *                                       devolución/aprobación de Cajas.
 *
 * Nota: usar siempre la versión `select2.full.min.js` (que incluye los módulos
 * `select2/compat/inputData`, `select2/compat/multipleSelection`, etc.). Si
 * la versión core se carga por algún motivo y Select2 falla al inicializar un
 * `<select multiple>` o con `data-tags`, este wrapper reemplaza la versión
 * core por la versión full automáticamente.
 */

(function () {
	var FULL_SELECT2_URL = '/assets/select2/js/select2.full.min.js';
	var SELECTORS = '[data-toggle="select"], .js-basic-multiple';
	var fullVersionLoaded = false;
	var select2Loading = null;

	function loadSelect2() {
		if (typeof window.jQuery === 'undefined') {
			return Promise.reject(new Error('jQuery no está disponible.'));
		}
		if (typeof jQuery.fn.select2 !== 'undefined' && !needsFullVersion($(document))) {
			fullVersionLoaded = true;
			return Promise.resolve();
		}
		if (select2Loading) return select2Loading;
		select2Loading = new Promise(function (resolve, reject) {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = FULL_SELECT2_URL;
			script.onload = function () {
				fullVersionLoaded = true;
				resolve();
			};
			script.onerror = function () {
				select2Loading = null;
				reject(new Error('No se pudo cargar ' + FULL_SELECT2_URL));
			};
			document.head.appendChild(script);
		});
		return select2Loading;
	}

	function needsFullVersion($context) {
		if (typeof window.jQuery === 'undefined') return false;
		var $scope = $context ? $context.find(SELECTORS) : $(SELECTORS);
		var $candidate = $scope.filter('select[multiple], [data-tags]');
		if ($candidate.length === 0) return false;
		for (var i = 0; i < $candidate.length; i++) {
			var el = $candidate[i];
			if (el.tagName === 'SELECT' && el.multiple) return true;
			if (jQuery(el).data('tags') !== undefined) return true;
		}
		return false;
	}

	function safeInit($this) {
		var options = {
			dropdownParent: $this.closest('.modal').length ? $this.closest('.modal') : $(document.body),
			minimumResultsForSearch: $this.data('minimum-results-for-search'),
		};
		try {
			$this.select2(options);
		} catch (err) {
			console.warn('[theme/select2] No se pudo inicializar Select2 sobre el elemento:', $this[0], err);
		}
	}

	function initIn($context) {
		if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;
		var $scope = $context ? $context.find(SELECTORS) : $(SELECTORS);
		if (!$scope.length) return;
		$scope.each(function () {
			var $this = $(this);
			if (!$this.length || !$this[0]) return;
			if ($this[0].tagName !== 'SELECT') return;
			if ($this.hasClass('select2-hidden-accessible')) return;
			safeInit($this);
		});
	}

	function ensureFullAndInit($context) {
		if (typeof window.jQuery === 'undefined') return Promise.resolve();
		// Si Select2 ya está cargado y no se requiere la versión full, inicializa directo.
		if (!fullVersionLoaded && typeof jQuery.fn.select2 !== 'undefined' && !needsFullVersion($context)) {
			initIn($context);
			return Promise.resolve();
		}
		return loadSelect2()
			.then(function () {
				initIn($context);
			})
			.catch(function (err) {
				console.warn('[theme/select2] No se pudo cargar Select2:', err);
			});
	}

	// API pública: las vistas la llaman tras renderizar.
	//   window.Select2.init($context?) -> inicializa selects en el contexto.
	window.Select2 = {
		init: function ($context) {
			return ensureFullAndInit($context || (window.jQuery ? $(document) : null));
		},
	};

	function bootstrapDocument() {
		ensureFullAndInit(window.jQuery ? $(document) : null);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', bootstrapDocument);
	} else {
		bootstrapDocument();
	}
})();