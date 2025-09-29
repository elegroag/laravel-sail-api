('use strict');

var Navbar = (function () {
	// Variables

	var $nav = $('.navbar-nav, .navbar-nav .nav');
	var $collapse = $('.navbar .collapse');
	var $dropdown = $('.navbar .dropdown');

	// Methods

	function accordion($this) {
		$this.closest($nav).find($collapse).not($this).collapse('hide');
	}

	function closeDropdown($this) {
		var $dropdownMenu = $this.find('.dropdown-menu');

		$dropdownMenu.addClass('close');

		setTimeout(function () {
			$dropdownMenu.removeClass('close');
		}, 200);
	}

	// Events

	$collapse.on({
		'show.bs.collapse': function () {
			accordion($(this));
		},
	});

	$dropdown.on({
		'hide.bs.dropdown': function () {
			closeDropdown($(this));
		},
	});
})();