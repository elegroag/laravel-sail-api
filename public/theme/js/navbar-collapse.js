var NavbarCollapse = (function () {
	// Variables

	var $nav = $('.navbar-nav'),
		$collapse = $('.navbar .navbar-custom-collapse');

	// Methods

	function hideNavbarCollapse($this) {
		$this.addClass('collapsing-out');
	}

	function hiddenNavbarCollapse($this) {
		$this.removeClass('collapsing-out');
	}

	// Events

	if ($collapse.length) {
		$collapse.on({
			'hide.bs.collapse': function () {
				hideNavbarCollapse($collapse);
			},
		});

		$collapse.on({
			'hidden.bs.collapse': function () {
				hiddenNavbarCollapse($collapse);
			},
		});
	}
})();
