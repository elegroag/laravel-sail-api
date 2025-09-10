function reporte_novedades() {
	var validator = $('#form').validate({
		rules: {
			fecini: { required: true },
			fecfin: { required: true },
		},
	});
	if (!$('#form').valid()) {
		return;
	}
	$('#form').submit();
}
