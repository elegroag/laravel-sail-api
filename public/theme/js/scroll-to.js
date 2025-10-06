('use strict');

var ScrollTo = (function () {
	var $scrollTo = $('.scroll-me, [data-scroll-to], .toc-entry a');

	function scrollTo($this) {
		var $el = $this.attr('href');
		var offset = $this.data('scroll-to-offset') ? $this.data('scroll-to-offset') : 0;
		var options = {
			scrollTop: $($el).offset().top - offset,
		};
		$('html, body').stop(true, true).animate(options, 600);
		event.preventDefault();
	}

	if ($scrollTo.length) {
		$scrollTo.on('click', function (event) {
			scrollTo($(this));
		});
	}
})();