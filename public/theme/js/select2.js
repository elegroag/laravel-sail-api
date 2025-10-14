'use strict';

var Select2 = (function () {
	var $select = $('[data-toggle="select"]');

	function init($this) {
		var options = {
			dropdownParent: $this.closest('.modal').length ? $this.closest('.modal') : $(document.body),
			minimumResultsForSearch: $this.data('minimum-results-for-search'),
		};
		$this.select2(options);
	}

	if ($select.length) {
		$select.each(function () {
			init($(this));
		});
	}
})();