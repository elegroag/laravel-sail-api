import { Utils, $Kumbia } from '@/Utils';

class AportesView extends Backbone.View {
	constructor(options) {
		super(options);
	}

	get className() {
		return 'col';
	}

	initialize() {
		this.template = document.getElementById('tmp_aportes').innerHTML;
	}

	render() {
		const template = _.template(this.template);
		this.$el.html(template(this.collection));
		this.__afterRender();
		return this;
	}

	get events() {
		return {
			"click [data-toggle='event-cuenta']": 'cambioCuenta',
			"click [data-toggle='info']": 'infoDetalle',
		};
	}

	buscarAportesEmpresa() {
		$.ajax({
			method: 'POST',
			dataType: 'JSON',
			cache: false,
			url: Utils.getKumbiaURL($Kumbia.controller + '/aportes/' + _ID),
			data: {},
			beforeSend: function (xhr) {
				Swal.fire({
					html: "<p class='text-center' style='font-size:1.2rem'><i class='fa fa-spinner fa-spin fa-2x fa-fw'></i> Procesando solicitud...</p>",
					icon: false,
					showCloseButton: false,
					showConfirmButton: false,
					allowOutsideClick: false,
					allowEscapeKey: false,
				});
			},
		})
			.done(function (response) {
				Swal.close();
				if (response.success) {
					load_aportes(response.data);
				} else {
					Swal.fire({
						title: 'Notificación Error',
						text: response.msj,
						icon: 'warning',
						showConfirmButton: false,
						showCloseButton: true,
						timer: 10000,
					});
					return false;
				}
			})
			.fail(function (err) {
				Swal.fire({
					title: 'Notificación Error',
					text: err.responseText,
					icon: 'warning',
					showConfirmButton: false,
					showCloseButton: true,
				});
			});
	}

	loadAportes(data) {
		let _template = _.template($('#tmp_aportes').html());
		$('#show_aportes').html(
			_template({
				aportes: data,
			}),
		);

		if (data.length > 0) {
			$('#table_aportes').DataTable({
				paging: true,
				pageLength: 15,
				search: true,
				pagingType: 'full_numbers',
				info: true,
				order: [[0, 'desc']],
				language: {
					processing: 'Procesando...',
					lengthMenu: 'Mostrar _MENU_ resultados por pagínas',
					zeroRecords: 'No se encontraron resultados',
					info: 'Mostrando pagína _PAGE_ de _PAGES_',
					infoEmpty: 'No records available',
					infoFiltered: '(filtered from _MAX_ total records)',
					emptyTable: 'Ningún dato disponible en esta tabla',
					search: 'Buscar',
					paginate: {
						next: 'siguiente',
						previus: 'anterior',
						first: 'primero',
						last: 'ultimo',
					},
					loadingRecords: 'Cargando...',
					buttons: {
						copy: 'Copiar',
						colvis: 'Visibilidad',
						collection: 'Colección',
						colvisRestore: 'Restaurar visibilidad',
						copyKeys:
							'Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.',
						copySuccess: {
							1: 'Copiada 1 fila al portapapeles',
							_: 'Copiadas %d fila al portapapeles',
						},
					},
				},
				dom: 'Bfrtip',
				buttons: ['csv', 'print'],
			});
			$('.dt-button').addClass('btn btn-primary btn-sm');
		}
	}

	__afterRender() {}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { AportesView };
