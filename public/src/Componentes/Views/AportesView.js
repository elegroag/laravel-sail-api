import { $Kumbia, Utils } from '@/Utils';
import { ModelView } from '@/Common/ModelView';
import { AportesModel } from '@/Componentes/Models/AportesModel';

class AportesView extends ModelView {
	constructor(options) {
		super({
			...options,
			onRender: () => this.afterRender(),
		});
		this.template = _.template(document.getElementById('tmp_aportes').innerHTML);
	}

	getTemplateRows() {
		return `<tr>
				<td><%=perapo%></td>
				<td><%=fecrec%></td>
				<td><%=fecsis%></td>
				<td><%=cedtra%></td>
				<td><%=codsuc%></td>
				<td><%=numero%></td>
				<td><%=valapo%></td>
				<td><%=valnom%></td>
		</tr>`;
	}

	afterRender() {
		let tpl = _.template(this.getTemplateRows());
		if (_.size(this.collection) > 0) {
			const html = this.collection.map((model) => {
				return tpl(model.toJSON());
			});
			this.$el.find('#show_aportes').html(html.join(''));
		} else {
			const empty = new AportesModel();
			this.$el.find('#show_aportes').html(tpl(empty.toJSON()));
			this.$el
				.find('#renderAlert')
				.html(
					'<div class="alert alert-danger">No hay aportes aplicados por parte de la empresa.</div>',
				);
		}

		if (_.size(this.collection) > 0) {
			this.$el.find('#table_aportes').DataTable({
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
						next: '<i class="fa fa-angle-right"></i>',
						previus: '<i class="fa fa-angle-left"></i>',
						first: '<i class="fa fa-angle-double-left"></i>',
						last: '<i class="fa fa-angle-double-right"></i>',
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
			this.$el.find('.dt-paging').css('float', 'right');
			this.$el.find('.dt-button').addClass('btn btn-primary btn-sm');
		}
	}

	remove() {
		this.stopListening();
		Backbone.View.prototype.remove.call(this);
	}
}

export { AportesView };
