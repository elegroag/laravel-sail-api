var Messages = (function () {
	function display(messages, clase) {
		swal.fire(messages, '', clase);
		Swal.fire({
			type: clase,
			title: messages,
			showConfirmButton: false,
			timer: 1500,
		});
	}
	return {
		display: display,
	};
})();
